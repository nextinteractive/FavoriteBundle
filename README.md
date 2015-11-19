# FavoriteBundle

## Installation

Set up the bundle in ``bundles.yml`` file:

```yaml
// others bundle
fav: Lpdigital\Bundle\FavoriteBundle\FavoriteBundle
```

Then install the table **user_bookmarks**:

```bash
./backbee bundle:install fav --force 
```

That's it. The bundle provide an admin interface that allow any user to configure favorites blocks and provide listener to add a new "Favoris" category when we have to select a block in contribution mode.
