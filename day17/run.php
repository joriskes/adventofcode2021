<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');

class Probe
{
    private int $px;
    private int $py;
    private int $vx;
    private int $vy;

    private int $target_from_x;
    private int $target_from_y;
    private int $target_to_x;
    private int $target_to_y;

    private int $heighest_y;
    private int $fired_vx;
    private int $fired_vy;

    public function __construct($target_from_x, $target_from_y, $target_to_x, $target_to_y)
    {
        $this->target_from_x = $target_from_x < $target_to_x ? $target_from_x : $target_to_x;
        $this->target_from_y = $target_from_y < $target_to_y ? $target_from_y : $target_to_y;
        $this->target_to_x = $target_from_x < $target_to_x ? $target_to_x : $target_from_x;
        $this->target_to_y = $target_from_y < $target_to_y ? $target_to_y : $target_from_y;
        $this->reset();
    }

    public function fire($vx, $vy)
    {
        $this->vx = $vx;
        $this->vy = $vy;
        $this->fired_vx = $vx;
        $this->fired_vy = $vy;
        $this->heighest_y = 0;
    }

    public function step()
    {
        // On each step, these changes occur in the following order:
        // The probe's x position increases by its x velocity.
        $this->px += $this->vx;
        // The probe's y position increases by its y velocity.
        $this->py += $this->vy;

        // Due to drag, the probe's x velocity changes by 1 toward the value 0; that is,
        // it decreases by 1 if it is greater than 0, increases by 1 if it is less than 0,
        // or does not change if it is already 0.
        $this->vx = max(0, $this->vx - 1); // $this->vx > 0 ? -1 : 1;

        // Due to gravity, the probe's y velocity decreases by 1.
        $this->vy--;

        if ($this->py > $this->heighest_y) {
            $this->heighest_y = $this->py;
        }
    }

    public function in_target(): bool
    {
//        p($this->px . ',' . $this->py . ' ' . $this->target_from_x . ',' . $this->target_from_y . ':' . $this->target_to_x . ',' . $this->target_to_y);
        return $this->px >= $this->target_from_x
            && $this->px <= $this->target_to_x
            && $this->py >= $this->target_from_y
            && $this->py <= $this->target_to_y;
    }

    public function past_target(): bool
    {
        return ($this->py < $this->target_to_y && $this->py < $this->target_from_y);
    }

    public function reset()
    {
        $this->px = 0;
        $this->py = 0;
        $this->vx = 0;
        $this->vy = 0;
    }

    public function getHeighestY()
    {
        return $this->heighest_y;
    }

    /**
     * @return int
     */
    public function getFiredVX(): int
    {
        return $this->fired_vx;
    }

    /**
     * @return int
     */
    public function getFiredVY(): int
    {
        return $this->fired_vy;
    }
}

preg_match_all('/target area: x=(\d+)\.\.(\d+), y=(-?\d+)\.\.(-?\d+)/ism', $input, $matches);

$target_from_x = intval($matches[1][0]);
$target_to_x = intval($matches[2][0]);
$target_from_y = intval($matches[3][0]);
$target_to_y = intval($matches[4][0]);

$probe = new Probe($target_from_x, $target_from_y, $target_to_x, $target_to_y);

$test_from_x = -max(abs($target_from_x), abs($target_to_x));
$test_to_x = max(abs($target_from_x), abs($target_to_x)) * 2;
$test_from_y = -max(abs($target_from_y), abs($target_to_y));
$test_to_y = max(abs($target_from_y), abs($target_to_y));

$highest_height = 0;
$highest_x = 0;
$highest_y = 0;
$in_target_count = 0;

for ($y = $test_from_y; $y < $test_to_y; $y++) {
    for ($x = $test_from_x; $x < $test_to_x; $x++) {
        $current_height = 0;
        $probe->reset();
        $probe->fire($x, $y);
        while (!$probe->past_target()) {
            $probe->step();
            if ($probe->in_target()) {
                $in_target_count++;
                if ($probe->getHeighestY() >= $highest_height) {
                    $highest_height = $probe->getHeighestY();
                    $highest_x = $probe->getFiredVX();
                    $highest_y = $probe->getFiredVY();
                }
                break;
            }
        }
    }
}

$part1 = $highest_height;
$part2 = $in_target_count;
p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
