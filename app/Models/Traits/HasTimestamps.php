<?php

namespace App\Models\Traits;

trait HasTimestamps
{
    /**
     * Atualiza os timestamps antes de salvar
     */
    protected function updateTimestamps(): void
    {
        if (!$this->exists) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');
    }

    /**
     * Sobrescreve o método save para atualizar timestamps
     */
    public function save(): bool
    {
        $this->updateTimestamps();
        return parent::save();
    }
} 