<?php

namespace Tests\Unit;

use App\Services\Xibo\XiboService;
use PHPUnit\Framework\TestCase;

class XiboServiceTest extends TestCase
{
    public function test_it_trims_xibo_tag_names_and_values(): void
    {
        $tags = (new XiboService)->normalizeTags([
            'tags' => [
                ['tag' => ' loc_tipo ', 'value' => ' bar '],
                ' loc_sector | hosteleria ',
            ],
            'web_visible' => ' true ',
            'com_estado' => ' disponible ',
        ]);

        $this->assertSame('bar', $tags['loc_tipo']);
        $this->assertSame('hosteleria', $tags['loc_sector']);
        $this->assertSame('true', $tags['web_visible']);
        $this->assertSame('disponible', $tags['com_estado']);
    }
}
