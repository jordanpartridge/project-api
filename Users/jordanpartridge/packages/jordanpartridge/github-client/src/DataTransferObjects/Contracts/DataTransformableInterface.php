<?php

namespace JordanPartridge\GithubClient\DataTransferObjects\Contracts;

interface DataTransformableInterface
{
    /**
     * Transform raw API response to DTO
     *
     * @param  array  $data  Raw API response data
     */
    public static function fromApiResponse(array $data): static;
}
