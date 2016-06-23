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

use BackBee\Bundle\AbstractAdminBundleController,
    BackBee\ClassContent\AbstractClassContent;

use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

use LpDigital\Bundle\FavoriteBundle\Entity\BookMark;

/**
 * @author Mickaël Andrieu <mickael.andrieu@lp-digital.fr>
 * @author Marian Hodis <marian.hodis@lp-digital.fr>
 */
class AdminController extends AbstractAdminBundleController
{
    /**
     * Class content manager
     * 
     * @var BackBee\ClassContent\ClassContentManager
     */
    private $manager;
    
    /**
     * Access to default bookmarks
     * 
     * @var bool
     */
    private $accessToDefault = false;

    public function __construct(\BackBee\BBApplication $app)
    {
        parent::__construct($app);

        try {
            $this->accessToDefault = $this->isGranted('EDIT', $this->bundle);
        } catch (AuthenticationCredentialsNotFoundException $e) {

        }
    }

    /**
     * List all available class content ordered by category
     *
     * @param bool $reload
     */
    public function indexAction($reload = false)
    {
        $classContents = $this->getClassContents();
        $userBookMarks = $this->getEntityManager()
            ->getRepository('LpDigital\Bundle\FavoriteBundle\Entity\BookMark')->findOneByUserId($this->getUser()->getId());

        if (!empty($userBookMarks)) {
            $userBookMarks = $userBookMarks->getBookMarks();
        }

        return $this->render('Admin/Index.twig', [
            'classContents' => $classContents,
            'bookMarks' => $userBookMarks,
            'accessToDefault' => $this->accessToDefault,
            'reload' => $reload
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
            ->getRepository('LpDigital\Bundle\FavoriteBundle\Entity\BookMark')->findOneByUserId($this->getUser()->getId());

        if (empty($userBookMarks)) {
            $userBookMarks = new BookMark();
            $userBookMarks->setUserId($this->getUser()->getId());
        }
        $userBookMarks->setBookMarks($bookmarkedBlocks);

        $this->getEntityManager()->persist($userBookMarks);
        $this->getEntityManager()->flush();

        $this->notifyUser(self::NOTIFY_SUCCESS, 'Your favourites are successfuly saved.');

        return $this->indexAction(true);
    }
    
    /**
     * Default bookmarks action
     * 
     * @param bool $reload
     */
    public function defaultBookMarksAction($reload = false)
    {
        if (!$this->accessToDefault) {
            $this->notifyUser(self::NOTIFY_ERROR, 'Permission denied!');
            return $this->render('Admin/BackToIndex.twig');
        }

        $classContents = $this->getClassContents();
        $sites = $this->getEntityManager()
            ->getRepository('\BackBee\Site\Site')->findAll();
        $bookMarks = $this->getEntityManager()
            ->getRepository('LpDigital\Bundle\FavoriteBundle\Entity\BookMark')->findAll();

        return $this->render('Admin/DefaultBookMarks.twig', [
            'classContents' => $classContents,
            'bookMarks' => $bookMarks,
            'sites' => $sites,
            'reload' => $reload
        ]);
    }
    
    /**
     * Save default favorites action
     */
    public function saveDefaultFavoritesAction()
    {
        $bookmarkedBlocks = $this->getRequest()->request->all();
        $bookMarksNotToBeRemoved = array();
        foreach($bookmarkedBlocks as $classContent => $sites) {
            $classContent = str_replace("[]","",$classContent);
            $alreadyBookMarkedBlocks = $this->getEntityManager()
                ->getRepository('LpDigital\Bundle\FavoriteBundle\Entity\BookMark')->findOneDefaultBookMark($classContent);
            
            if (empty($alreadyBookMarkedBlocks)) {
                $alreadyBookMarkedBlocks = new BookMark();
                $alreadyBookMarkedBlocks->setBookMarks(array($classContent));
            } 
            $alreadyBookMarkedBlocks->setSiteId($sites);

            $this->getEntityManager()->persist($alreadyBookMarkedBlocks);
            $this->getEntityManager()->flush();
            $bookMarksNotToBeRemoved[] = $alreadyBookMarkedBlocks;
        }
        $this->emptyDefaultFavorites($bookMarksNotToBeRemoved);
        $this->notifyUser(self::NOTIFY_SUCCESS, 'Your default favourites are successfuly saved.');

        return $this->defaultBookMarksAction(true);
    }
    
    /**
     * Remove all noot bookmarked class contents
     * 
     * @param array $bookMarksNotToBeRemoved
     */
    private function emptyDefaultFavorites(array $bookMarksNotToBeRemoved) 
    {
        $bookMarks = $this->getEntityManager()
            ->getRepository('LpDigital\Bundle\FavoriteBundle\Entity\BookMark')->findAllDefaultBookMarks();
        $diff = array_diff(
            array_map(function($object) {
                if(count($object->getBookMarks()) == 1) {
                    return $object->getBookMarks()[0];
                }
            }, $bookMarks),
            array_map(function($object) {
                if(count($object->getBookMarks()) == 1) {
                    return $object->getBookMarks()[0];
                }
            }, $bookMarksNotToBeRemoved));
        foreach($diff as $classContentToBeRemoved) {
            $toBeRemoved = $this->getEntityManager()
                ->getRepository('LpDigital\Bundle\FavoriteBundle\Entity\BookMark')->findOneDefaultBookMark($classContentToBeRemoved);
            $this->getEntityManager()->remove($toBeRemoved);
            $this->getEntityManager()->flush();
        }
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
