<?php

namespace ComposerInsights\Services;

use Carbon\Carbon;
use ComposerInsights\Formatters\NumberFormatter;
use ComposerInsights\Support\Icon;
use Symfony\Component\Console\Output\OutputInterface;

class ReportSummaryGenerator
{
    public function generate(array $insights, OutputInterface $output): void
    {
        $output->writeln("\n");
        $output->writeln(Icon::get('report')." Generating Report ....\n");

        $total = count($insights);

        $output->writeln(Icon::get('star')." Popular Packages:\n");
        $output->writeln($this->getStatLine($insights, 'popularity.downloads', Icon::get('download').'  Most Downloaded Package', '', ' Downloads'));
        $output->writeln($this->getStatLine($insights, 'popularity.stars', Icon::get('star').' Most Starred Package', '', ' Stars'));

        $output->writeln("\n".Icon::get('health')." Health Checks:\n");
        $output->writeln($this->getStatLine($insights, 'health.open_issues', Icon::get('bug').' Package with highest open issues', '', ' Issues').'  '. Icon::get('warning'));

        $outdatedCount = $this->generateOutdatedPackageSummary($output, $insights);

        $notUpdatedCount = $this->generateNotUpdatedPackageSummary($output, $insights);

        $output->writeln("\n".Icon::get('package')." {$total} analyzed | {$outdatedCount} outdated | {$notUpdatedCount} without recent updates");

        $output->writeln("\n<info>".Icon::get('done')." Done</info>");
    }

    /**
     * Get a summary line for the package with the highest value at the given key path.
     */
    private function getStatLine(array $insights, string $keyPath, string $label, string $prefix = '', string $suffix = ''): string
    {
        $topPackage = $this->getPackageWithMax($insights, $keyPath);
        $value = NumberFormatter::humanize($this->getNestedValue($topPackage, $keyPath));

        return "{$label}: {$topPackage['package']['name']} ({$prefix}{$value}{$suffix})";
    }

    /**
     * Find the package with the maximum value for a given key path.
     */
    private function getPackageWithMax(array $insights, string $keyPath): array
    {
        return array_reduce($insights, function ($carry, $item) use ($keyPath) {
            if (!$carry) {
                return $item;
            }

            $current = $this->getNestedValue($item, $keyPath);
            $max = $this->getNestedValue($carry, $keyPath);

            return $current > $max ? $item : $carry;
        });
    }

    /**
     * Get a nested value using dot notation (e.g., "popularity.downloads").
     */
    private function getNestedValue(array $array, string $path)
    {
        foreach (explode('.', $path) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return null;
            }
            $array = $array[$segment];
        }
        return $array;
    }

    private function generateOutdatedPackageSummary(OutputInterface $output, array $insights): int
    {
        $packages = $this->getOutdatedPackages($insights);
        
        if(empty($packages)) {
            $output->writeln("\n".Icon::get('done')." No outdated packages\n");
            return 0;
        }
        
        $output->writeln("\n".Icon::get('waiting')." Outdated Packages: \n");
        
        foreach ($packages as $package)
        {
            $output->writeln($this->getVersionDifferenceInfo($package));
        }

        return count($packages);
    } 

    private function getOutdatedPackages(array $insights): array
    {
        return array_filter($insights, function ($insight) {
            return $insight['version']['is_outdated'];
        });
    }

    private function getVersionDifferenceInfo(array $insight): string
    {
        $latest = $insight['version']['latest'];
        $used = $insight['version']['used'];
        $name = $insight['package']['name'];

        return '- '.$name. ' '.
            $this->formatUsedVersion($used).' '.
            Icon::get('arrow').'  '.
            $this->formatLatestVersion($latest).' '.
            $this->getVersiondifferenceSuffix($used, $latest);
    }

    private function formatUsedVersion(string $used): string
    {
        return '<comment>'.$used.'</comment>';
    }

    private function formatLatestVersion(string $latest): string
    {
        return '<info>'.$latest.'</info>';
    }

    private function getVersiondifferenceSuffix(string $used, string $latest): string
    {
        $used = $this->normalizeVersion($used);
        $latest = $this->normalizeVersion($latest);

        [$cMajor, $cMinor, $cPatch] = array_map('intval', explode('.', ltrim($used, 'v')));
        [$lMajor, $lMinor, $lPatch] = array_map('intval', explode('.', ltrim($latest, 'v')));

        $diff = match (true) {
            $lMajor > $cMajor => 'major',
            $lMinor > $cMinor => 'minor',
            $lPatch > $cPatch => 'patch',
            default => '',
        };

        $icon = match ($diff) {
            'major' => Icon::get('major'),
            'minor' => Icon::get('minor'),
            'patch' => Icon::get('patch'),
            default => '',
        };

        return '('.$icon.' Behind by '.$diff.' version)';
    }

    private function normalizeVersion(string $version): string
    {
        $parts = explode('.', ltrim($version, 'v'));
        $parts = array_map('intval', $parts);

        return implode('.', [
            $parts[0] ?? 0,
            $parts[1] ?? 0,
            $parts[2] ?? 0,
        ]);
    }

    private function generateNotUpdatedPackageSummary(OutputInterface $output, array $insights): int
    {
        $packages = $this->getNotUpdatedPackages($insights);

        if(empty($packages)) {
            $output->writeln("\n".Icon::get('done')." All packages are up to date recently \n");
            return 0;
        }

        $output->writeln("\n".Icon::get('downtrend')." Packages not updated recently: \n");
        
        foreach ($packages as $package)
        {
            $output->writeln($this->getNotUpdatedPackageInfo($package));
        }

        return count($packages);
    }

    private function getNotUpdatedPackages(array $insights): array
    {
        return array_filter($insights, function ($insight) {
            return $insight['maintenance']['is_stale'];
        });
    }

    private function getNotUpdatedPackageInfo(array $insight): string
    {
        $name = $insight['package']['name'];
        $updated = Carbon::parse($insight['maintenance']['updated_at'])->diffForHumans();

        return '- '.$name. ': '.
            'Last updated '.$updated;
    }
}
