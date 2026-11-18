<?php

namespace Tests\Unit\Shared\Query;

use App\Features\Shared\Query\BuilderFilter;
use App\Features\Shared\Query\QueryFilter;
use App\Features\Shared\Query\QueryOptions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueryModifiersTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_direction_falls_back_to_ascending(): void
    {
        User::factory()->create(['name' => 'Zara']);
        User::factory()->create(['name' => 'Ana']);

        $result = (new BuilderFilter)->paginate(
            builder: User::query(),
            modifiers: [
                QueryOptions::orderBy('name', 'sideways'),
                QueryOptions::limit(10),
                QueryOptions::offset(0),
            ],
            draw: 4,
        );

        $this->assertSame(4, $result['draw']);
        $this->assertSame('Ana', $result['data']->first()->name);
        $this->assertSame(2, $result['recordsTotal']);
    }

    public function test_filter_reduces_records_filtered_and_keeps_the_total(): void
    {
        User::factory()->create(['name' => 'Ana']);
        User::factory()->create(['name' => 'Beto']);

        $result = (new BuilderFilter)->paginate(
            builder: User::query(),
            modifiers: [
                QueryFilter::where('name', 'Ana'),
                QueryOptions::limit(10),
                QueryOptions::offset(0),
            ],
        );

        $this->assertSame(2, $result['recordsTotal']);
        $this->assertSame(1, $result['recordsFiltered']);
        $this->assertCount(1, $result['data']);
    }
}
