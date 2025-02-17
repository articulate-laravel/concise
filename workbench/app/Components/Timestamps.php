<?php
declare(strict_types=1);

namespace App\Components;

use Carbon\CarbonInterface;

class Timestamps
{
    protected CarbonInterface $createdAt;

    protected CarbonInterface $updatedAt;

    public function __construct(
        ?CarbonInterface $createdAt = null,
        ?CarbonInterface $updatedAt = null,
    )
    {
        if ($createdAt) {
            $this->createdAt = $createdAt;
        }

        if ($updatedAt) {
            $this->updatedAt = $updatedAt;
        }
    }

    /**
     * @return \Carbon\CarbonInterface
     */
    public function getCreatedAt(): CarbonInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \Carbon\CarbonInterface $createdAt
     *
     * @return Timestamps
     */
    public function setCreatedAt(CarbonInterface $createdAt): Timestamps
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \Carbon\CarbonInterface
     */
    public function getUpdatedAt(): CarbonInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \Carbon\CarbonInterface $updatedAt
     *
     * @return Timestamps
     */
    public function setUpdatedAt(CarbonInterface $updatedAt): Timestamps
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
