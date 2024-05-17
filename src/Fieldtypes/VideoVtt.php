<?php

namespace Tv2regionerne\StatamicVideo\Fieldtypes;

use Captioning\Format\WebvttCue;
use Captioning\Format\WebvttFile;
use Statamic\Fields\Fieldtype;

class VideoVtt extends Fieldtype
{
    protected static $title = 'Video VTT';

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
                'instructions' => __('What video field handle the trimmer should use.'),
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
    //     $vtt = new WebvttFile();
    //     $vtt->loadFromString($data);

    //     return $data;
    // }

    // public function augment($value): array
    // {
    //     $output = [
    //         'error' => false,
    //     ];
    //     try {
    //         $vtt = new WebvttFile();
    //         $vtt->loadFromString($value);

    //         $cues = [];

    //         /** @var WebvttCue $cue */
    //         foreach ($vtt->getCues() as $cue) {
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
