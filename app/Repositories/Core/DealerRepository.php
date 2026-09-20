<?php

namespace App\Repositories\Core;

use App\Repositories\BaseRepository;
use App\Models\Dealer;

class DealerRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Dealer $model
    ) {
        $this->model = $model;
    }
}
