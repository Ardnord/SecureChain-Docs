<?php

namespace App\Models;

use CodeIgniter\Model;

class BlockModel extends Model
{
    protected $DBGroup          = 'userdb';  // Use user database
    protected $table            = 'blockchain';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $useTimestamps = true;
    protected $createdField  = 'timestamp';
    protected $updatedField  = '';

    protected $allowedFields    = [
        'nomor_permohonan',
        'nomor_dokumen',
        'tanggal_dokumen',
        'tanggal_filing',
        'dokumen_base64',
        'ip_address',
        'block_hash',
        'previous_hash'
    ];

    public function getLatestBlock(): ?array
    {
        return $this->orderBy('id', 'DESC')->first();
    }

    public function getAllBlocks(): array
    {
        return $this->orderBy('id', 'ASC')->findAll();
    }

    public function getBlockByHash(string $blockHash): ?array
    {
        return $this->where('block_hash', $blockHash)->first();
    }

    public function search(string $keyword): array
    {
        $sanitizedKeyword = esc($keyword);

        return $this->select('id, nomor_permohonan, nomor_dokumen, tanggal_dokumen, tanggal_filing, block_hash, previous_hash, timestamp')
            ->groupStart()
            ->like('nomor_permohonan', $sanitizedKeyword)
            ->orLike('nomor_dokumen', $sanitizedKeyword)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->paginate(10);
    }
}
