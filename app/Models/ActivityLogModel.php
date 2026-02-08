<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $DBGroup          = 'admindb';  // Use admin database
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $allowedFields    = [
        'action_type',
        'block_id',
        'identifier',
        'original_data',
        'modified_data',
        'status',
        'description',
        'ip_address',
        'user_agent'
    ];

    public function logActivity(array $logData): bool
    {
        $data = [
            'action_type' => $logData['action_type'],
            'block_id' => $logData['block_id'] ?? null,
            'identifier' => $logData['identifier'] ?? null,
            'original_data' => isset($logData['original_data']) ? json_encode($logData['original_data']) : null,
            'modified_data' => isset($logData['modified_data']) ? json_encode($logData['modified_data']) : null,
            'status' => $logData['status'] ?? 'INFO',
            'description' => $logData['description'] ?? '',
            'ip_address' => $logData['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $logData['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];

        return $this->insert($data);
    }

    public function getLogsByType(?string $actionType = null, int $limit = 100): array
    {
        $builder = $this;

        if ($actionType) {
            $builder = $builder->where('action_type', $actionType);
        }

        return $builder->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getLogsByBlockId(int $blockId): array
    {
        return $this->where('block_id', $blockId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getStatistics(): array
    {
        return [
            'total_logs' => $this->countAllResults(false),
            'total_manipulations' => $this->where('action_type', 'MANIPULATE')->countAllResults(false),
            'total_recoveries' => $this->where('action_type', 'RECOVER')->countAllResults(false),
            'total_creates' => $this->where('action_type', 'CREATE')->countAllResults(false),
            'total_updates' => $this->where('action_type', 'UPDATE')->countAllResults(false),
            'total_deletes' => $this->where('action_type', 'DELETE')->countAllResults(false),
            'total_checks' => $this->where('action_type', 'CHECK')->countAllResults(false),
        ];
    }

    public function getRecentLogs(int $limit = 50, int $offset = 0): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    public function getDashboardLogs(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function cleanOldLogs(int $days = 90): bool
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('created_at <', $date)->delete();
    }

    public function getLastCheckTime(): ?array
    {
        return $this->where('action_type', 'CHECK')
            ->orderBy('created_at', 'DESC')
            ->first();
    }
}
