<?php

namespace Tests\Unit;

use App\Services\Posts\HashtagNormalizer;
use PHPUnit\Framework\TestCase;

class HashtagNormalizerTest extends TestCase
{
    public function testItNormalizesTags(): void
    {
        $normalizer = new HashtagNormalizer();

        $this->assertSame(
            ['travel', 'summer', 'sea'],
            $normalizer->normalize('  #Travel   summer sea  summer ')
        );
    }

    public function testItValidatesTagString(): void
    {
        $normalizer = new HashtagNormalizer();

        $this->assertTrue($normalizer->isValid('travel summer sea'));
        $this->assertFalse($normalizer->isValid(''));
    }
}
