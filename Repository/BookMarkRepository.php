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

namespace LpDigital\Bundle\FavoriteBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Default book mark repository
 * 
 * @author Marian Hodis <marian.hodis@lp-digital.fr>
 */
class BookMarkRepository extends EntityRepository
{
    /**
     * Find default book mark by the site uid
     * 
     * @param string $siteUid
     * @return array
     */
    public function findBySiteUid($siteUid)
    {
        $query = $this->createQueryBuilder('d');
        $query->where(
            $query->expr()->like('d.siteId', $query->expr()->literal('%'.$siteUid.'%'))
        );
        return $query->getQuery()->getResult();
    }
    
    public function findAllDefaultBookMarks()
    {
        $query = $this->createQueryBuilder('d');
        $query->where('d.siteId IS NOT NULL');
        return $query->getQuery()->execute();
    }
    
    /**
     * Find one default book mark
     * 
     * @param string $bookMark
     * @return \LpDigital\Bundle\FavoriteBundle\Entity\BookMark
     */
    public function findOneDefaultBookMark($bookMark)
    {
        $query = $this->createQueryBuilder('d');
        $query->where('d.siteId IS NOT NULL');
        $query->andWhere('d.bookmarks = :bookMark');
        $query->setParameter('bookMark', $bookMark);
        return $query->getQuery()->getOneOrNullResult();
    }
}
