<?php

namespace App;

enum TestResultStatus: string
{
    case Pending = 'pending';
    case Passed = 'passed';
    case Failed = 'failed';
    case Skipped = 'skipped';
    case Blocked = 'blocked';
}
