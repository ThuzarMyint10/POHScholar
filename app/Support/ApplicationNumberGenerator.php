<?php

namespace App\Support;

use App\Models\ApplicationNumberSeq;

class ApplicationNumberGenerator
{
    /**
     * Build a safe, unique, sequential application number like
     * "POH-2026-000123" by inserting into the tiny append-only
     * application_number_seq table and using its auto-increment id -
     * avoids any race condition between two students submitting at once.
     */
    public static function next(int $studentId): string
    {
        $seq = ApplicationNumberSeq::query()->create(['student_id' => $studentId]);

        return sprintf('POH-%s-%06d', date('Y'), $seq->id);
    }
}
