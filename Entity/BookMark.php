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

namespace LpDigital\Bundle\FavoriteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Mickaël Andrieu <mickael.andrieu@lp-digital.fr>
 * @author Marian Hodis <marian.hodis@lp-digital.fr>
 *
 * @ORM\Entity(repositoryClass="LpDigital\Bundle\FavoriteBundle\Repository\BookMarkRepository")
 * @ORM\Table(name="user_bookmarks")
 */
class BookMark
{
    /**
     * Unique identifier
     * 
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer", name="user_id", nullable=true)
     */
    private $userId;
    
    /**
     * Site id
     * 
     * @var array
     * @ORM\Column(type="simple_array", name="site_id", nullable=true)
     */
    private $siteId;

    /**
     * @var array
     * @ORM\Column(type="simple_array", name="bookmarks")
     */
    private $bookmarks;
    
    /**
     * Get id
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Get user id
     * 
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * Set user id
     * 
     * @param int $userId
     * @return \LpDigital\Bundle\FavoriteBundle\Entity\BookMark
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }
    
    /**
     * Site id
     * 
     * @return array
     */
    public function getSiteId()
    {
        return $this->siteId;
    }
    
    /**
     * Set site id
     * 
     * @param array $siteId
     * @return \LpDigital\Bundle\FavoriteBundle\Entity\DefaultBookMark
     */
    public function setSiteId(array $siteId)
    {
        $this->siteId = $siteId;
        return $this;
    }
    
    /**
     * Get book marks
     * 
     * @return array
     */
    public function getBookMarks()
    {
        return $this->bookmarks;
    }

    /**
     * Set book marks
     * 
     * @param array $bookmarks
     * @return \LpDigital\Bundle\FavoriteBundle\Entity\BookMark
     */
    public function setBookMarks(array $bookmarks)
    {
        $this->bookmarks = $bookmarks;
        return $this;
    }
}
