<?php

namespace App\Support;

/**
 * Central catalog of the read-only "Additional Access" functions an admin can
 * grant to any non-admin user. Each entry maps a permission key to the
 * read-only viewer page that renders aggregate data (Option A: view all).
 *
 * Adding a new viewable function is as simple as adding one entry here plus
 * the matching route/controller/view.
 */
class ViewPermissions
{
    /**
     * @return array<string, array{label: string, icon: string, route: string, description: string}>
     */
    public static function catalog(): array
    {
        return [
            'view.profiles' => [
                'label' => 'AF/AD Profiles',
                'icon' => 'people',
                'route' => 'access.profiles.index',
                'description' => 'View all AF/AD profiles (read-only).',
            ],
            'view.appointments' => [
                'label' => 'Appointments',
                'icon' => 'calendar3',
                'route' => 'access.appointments.index',
                'description' => 'View all appointments (read-only).',
            ],
            'view.claims' => [
                'label' => 'Claims',
                'icon' => 'file-earmark-text',
                'route' => 'access.claims.index',
                'description' => 'View all claim history (read-only).',
            ],
            'view.submissions' => [
                'label' => 'Submissions',
                'icon' => 'camera-video',
                'route' => 'access.submissions.index',
                'description' => 'View all submissions (read-only).',
            ],
            'view.certificates' => [
                'label' => 'Certificates',
                'icon' => 'award',
                'route' => 'access.certificates.index',
                'description' => 'View all certificates (read-only).',
            ],
            'view.reports' => [
                'label' => 'Reports',
                'icon' => 'bar-chart',
                'route' => 'access.reports.index',
                'description' => 'View aggregate reports and export CSV (read-only).',
            ],
        ];
    }

    /**
     * All valid permission keys.
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(static::catalog());
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, static::catalog());
    }
}
