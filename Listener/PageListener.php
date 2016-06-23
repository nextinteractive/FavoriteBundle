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

namespace LpDigital\Bundle\FavoriteBundle\Listener;

use BackBee\Event\Event;

/**
 * @author Florian Kroockmann <florian.kroockmann@lp-digital.fr>
 */
class PageListener
{
     public static function onPageRender(Event $event)
     {
        $renderer = $event->getEventArgs();

        $user = $event->getDispatcher()->getApplication()->getBBUserToken();
        if (null !== $user) {
            $renderer->addFooterScript($renderer->getResourceUrl('js/favorite_hook.js'));
        }
     }
}