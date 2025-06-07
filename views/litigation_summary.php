<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 lg:tw-grid-cols-5 tw-gap-4 tw-mb-4">

    <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-p-4">
        <p class="tw-text-sm tw-font-medium tw-text-gray-500">
            <?= _l('total_cases'); ?>
        </p>
        <p class="tw-mt-1 tw-text-xl tw-font-semibold tw-text-neutral-800">
            <?= (int)$summary['total']; ?>
        </p>
    </div>

    <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-p-4">
        <p class="tw-text-sm tw-font-medium tw-text-gray-500">
            <?= _l('ongoing_cases'); ?>
        </p>
        <p class="tw-mt-1 tw-text-xl tw-font-semibold tw-text-blue-600">
            <?= (int)$summary['ongoing']; ?>
        </p>
    </div>

    <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-p-4">
        <p class="tw-text-sm tw-font-medium tw-text-gray-500">
            <?= _l('closed_cases'); ?>
        </p>
        <p class="tw-mt-1 tw-text-xl tw-font-semibold tw-text-green-600">
            <?= (int)$summary['closed']; ?>
        </p>
    </div>

    <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-p-4">
        <p class="tw-text-sm tw-font-medium tw-text-gray-500">
            <?= _l('pending_hearings'); ?>
        </p>
        <p class="tw-mt-1 tw-text-xl tw-font-semibold tw-text-yellow-600">
            <?= (int)$summary['pending_hearings']; ?>
        </p>
    </div>

</div>
