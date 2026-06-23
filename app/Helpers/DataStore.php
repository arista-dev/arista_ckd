<?php

namespace App\Helpers;

class DataStore
{
    // ─── Generic read/write ───────────────────────────────────────────────────

    public static function read(string $file): array
    {
        $path = storage_path("app/data/{$file}.php");
        if (!file_exists($path)) {
            return [];
        }
        $data = require $path;
        return is_array($data) ? $data : [];
    }

    public static function write(string $file, array $data): void
    {
        $path = storage_path("app/data/{$file}.php");
        $export = var_export($data, true);
        file_put_contents($path, "<?php\n\nreturn {$export};\n");
    }

    // ─── Users ────────────────────────────────────────────────────────────────

    public static function findUser(string $username, string $password): ?array
    {
        foreach (self::read('users') as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                return $user;
            }
        }
        return null;
    }

    // ─── Models / Components ─────────────────────────────────────────────────

    public static function getModels(): array
    {
        return self::read('models');
    }

    public static function getModelComponents(string $modelName): array
    {
        $models = self::getModels();
        return $models[$modelName]['components'] ?? [];
    }

    // ─── Receivings ───────────────────────────────────────────────────────────

    public static function getReceivings(): array
    {
        return self::read('receivings');
    }

    public static function saveReceiving(array $receiving): void
    {
        $all = self::getReceivings();
        $all[] = $receiving;
        self::write('receivings', $all);
    }

    public static function updateReceiving(string $id, array $updates): void
    {
        $all = self::getReceivings();
        foreach ($all as &$r) {
            if ($r['id'] === $id) {
                $r = array_merge($r, $updates);
                break;
            }
        }
        self::write('receivings', $all);
    }

    public static function findReceiving(string $id): ?array
    {
        foreach (self::getReceivings() as $r) {
            if ($r['id'] === $id) return $r;
        }
        return null;
    }

    public static function generateReceivingNo(): string
    {
        $all   = self::getReceivings();
        $count = count($all) + 1;
        return 'RCV-' . date('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    // ─── Inspections ─────────────────────────────────────────────────────────

    public static function getInspections(): array
    {
        return self::read('inspections');
    }

    public static function saveInspection(array $inspection): void
    {
        $all = self::getInspections();
        $all[] = $inspection;
        self::write('inspections', $all);
    }

    public static function updateInspection(string $id, array $updates): void
    {
        $all = self::getInspections();
        foreach ($all as &$ins) {
            if ($ins['id'] === $id) {
                $ins = array_merge($ins, $updates);
                break;
            }
        }
        self::write('inspections', $all);
    }

    public static function findInspection(string $id): ?array
    {
        foreach (self::getInspections() as $ins) {
            if ($ins['id'] === $id) return $ins;
        }
        return null;
    }

    public static function generateInspectionNo(): string
    {
        $all   = self::getInspections();
        $count = count($all) + 1;
        return 'INS-' . date('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    // ─── Stats for Dashboard ─────────────────────────────────────────────────

    public static function getDashboardStats(): array
    {
        $receivings   = self::getReceivings();
        $inspections  = self::getInspections();

        $totalShortage = 0;
        $totalDamage   = 0;

        foreach ($inspections as $ins) {
            foreach ($ins['components'] ?? [] as $comp) {
                if (($comp['status'] ?? '') === 'SHORT')  $totalShortage++;
                if (($comp['status'] ?? '') === 'DAMAGE') $totalDamage++;
            }
        }

        return [
            'total_receiving'  => count($receivings),
            'total_inspection' => count($inspections),
            'total_shortage'   => $totalShortage,
            'total_damage'     => $totalDamage,
        ];
    }
}
