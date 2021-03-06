<?php

namespace Jkchr1s\StaticPageBlocks\Classes;

use Jkchr1s\StaticPageBlocks\Models\BlockSet;
use Jkchr1s\StaticPageBlocks\Models\BlockType;

class BlockTypeWidgetizer
{
    /**
     * @return array
     */
    public static function repeaterGroups()
    {
        $blockSets = BlockSet::all();
        $groups = $blockSets->isEmpty()
            ? []
            : [
                '__blockSetEmbed' => [
                    'name' => 'Embed Block Set',
                    'description' => 'Embeds a block set',
                    'icon' => 'icon-cubes',
                    'fields' => [
                        '__blockHeading' => [
                            'type' => 'section',
                            'label' => 'Embed Block Set'
                        ],
                        'blockSlug' => [
                            'type' => 'dropdown',
                            'options' => $blockSets->flatMap(function (BlockSet $bs) {
                                return [$bs->slug => $bs->title];
                            })->toArray()
                        ]
                    ]
                ]
            ];

        BlockType::all()->each(function(BlockType $bt) use(&$groups) {
            $slug = $bt->slug;
            if (empty($slug)) {
                return;
            }
            $groups[$bt->slug] = [
                'name' => $bt->title,
                'description' => empty($bt->description) ? 'No description provided' : $bt->description,
                'icon' => empty($bt->icon) ? 'oc-leaf' : $bt->icon,
                'fields' => [
                    '__blockHeading' => [
                        'type' => 'section',
                        'label' => $bt->title
                    ]
                ]
            ];
            foreach ($bt->blocks as $idx => $block) {
                $body = [];
                $name = null;
                foreach ($block as $key => $value) {
                    if ($key === '_group') {
                        $body['type'] = $value;
                    } elseif ($key === 'name') {
                        $name = $value;
                    } else {
                        $body[$key] = $value;
                    }
                }
                if (!empty($body) && isset($body['type'])) {
                    if (!empty($name)) {
                        $groups[$slug]['fields'][$name] = $body;
                    } elseif ($body['type'] === 'section') {
                        $groups[$slug]['fields']["section$idx"] = $body;
                    }
                }
            }
        });
        return $groups;
    }
}
