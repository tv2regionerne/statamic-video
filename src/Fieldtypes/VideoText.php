<?php

namespace Tv2regionerne\StatamicVideo\Fieldtypes;

use Captioning\Format\WebTextCue;
use Captioning\Format\WebTextFile;
use Statamic\Fields\Fieldtype;

class VideoText extends Fieldtype
{
    protected $defaultValue = [
        'chapters' => [
            [
                'start' => 0,
                'end' => 0,
                'title' => null,
            ],
        ],
    ];

    protected function configFieldItems(): array
    {
        return [
            'source' => [
                'type' => 'text',
                'display' => __('Video Source Field'),
                'instructions' => __('What video field handle the Timemer should use.'),
                'default' => 'video',
                'width' => 50,
                'validate' => [
                    'required',
                ],
            ],
        ];
    }

    // /**
    //  * The blank/default value.
    //  *
    //  * @return array
    //  */
    // public function defaultValue()
    // {
    //     return null;
    // }

    // /**
    //  * Pre-process the data before it gets sent to the publish page.
    //  *
    //  * @param  mixed  $data
    //  * @return array|mixed
    //  */
    // public function preProcess($data)
    // {
    //     return [
    //         'chapters' =>
    //     ];
    // }

    // /**
    //  * Process the data before it gets saved.
    //  *
    //  * @param  mixed  $data
    //  * @return array|mixed
    //  */
    // public function process($data)
    // {
    //     $Text = new WebTextFile();
    //     $Text->loadFromString($data);

    //     return $data;
    // }

    // public function augment($value): array
    // {
    //     $output = [
    //         'error' => false,
    //     ];
    //     try {
    //         $Text = new WebTextFile();
    //         $Text->loadFromString($value);

    //         $cues = [];

    //         /** @var WebTextCue $cue */
    //         foreach ($Text->getCues() as $cue) {
    //             $cues[] = [
    //                 'start' => $cue->getStart(),
    //                 'stop' => $cue->getStart(),
    //                 'text' => $cue->getText(),
    //                 //'textLines' => $cue->getTextLines(),
    //             ];
    //         }
    //         $output['cues'] = $cues;
    //     } catch (\Exception $exception) {
    //         $output['error'] = true;
    //         $output['error_msg'] = $exception->getMessage();
    //     }

    //     return $output;
    // }
}
