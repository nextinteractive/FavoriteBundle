<?php

/*
 * Copyright (c) 2011-2015 Lp digital system
 *
 * This file is part of FavoriteBundle.
 *
 * FavoriteBundle is free bundle: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FavoriteBundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FavoriteBundle. If not, see <http://www.gnu.org/licenses/>.
 */

namespace LpDigital\Bundle\FavoriteBundle\Controller;

use BackBee\Bundle\AbstractAdminBundleController;
use BackBee\ClassContent\AbstractClassContent;
use LpDigital\Bundle\FavoriteBundle\Entity\BookMark;

/**
 * @author      Mickaël Andrieu <mickael.andrieu@lp-digital.fr>
 */
class AdminController extends AbstractAdminBundleController
{
    private $manager;


    /**
     * List all available class content ordered by category
     *
     */
    public function indexAction()
    {
        $classContents = $this->getClassContents();
        $userBookMarks = $this->getEntityManager()
            ->getRepository('BackBee\Bundle\FavoriteBundle\Entity\BookMark')->findOneByUserId($this->getUser()->getId());

        if (!empty($userBookMarks)) {
            $userBookMarks = $userBookMarks->getBookMarks();
        }

        return $this->render('Admin/Index.twig', [
            'classContents' => $classContents,
            'bookMarks' => $userBookMarks
        ]);
    }

    /**
     * Save the user preferences
     *
     */
    public function saveFavoritesAction()
    {
        $bookmarkedBlocks = array_keys($this->getRequest()->request->all());
        $userBookMarks = $this->getEntityManager()
            ->getRepository('BackBee\Bundle\FavoriteBundle\Entity\BookMark')->findOneByUserId($this->getUser()->getId());

        if (empty($userBookMarks)) {
            $userBookMarks = new BookMark();
            $userBookMarks->setUserId($this->getUser()->getId());

        }
        $userBookMarks->setBookMarks($bookmarkedBlocks);

        $this->getEntityManager()->persist($userBookMarks);
        $this->getEntityManager()->flush();

        $this->notifyUser(self::NOTIFY_SUCCESS, 'Your favorites are successfuly saved.');

        return $this->indexAction();
    }

    /**
     * Returns ClassContentManager.
     *
     * @return BackBee\ClassContent\ClassContentManager
     */
    private function getClassContentManager()
    {
        if (null === $this->manager) {
            $this->manager = $this->getApplication()->getContainer()->get('classcontent.manager')
                ->setBBUserToken($this->getApplication()->getBBUserToken())
            ;
        }

        return $this->manager;
    }

    private function getClassContents()
    {
        $classNames = array_filter(
            $this->getClassContentManager()->getAllClassContentClassnames(),
            function ($className) {
                return false === strpos(
                    $className,
                    AbstractClassContent::CLASSCONTENT_BASE_NAMESPACE.'Element\\'
                );
            }
        );
        $contents = [];

        foreach($classNames as $className)
        {
            $content = new $className;
            if (null !== $content->getProperty('name')) {
                $contents[$className] = new $className;
            }
        }

        return $contents;
    }

}
