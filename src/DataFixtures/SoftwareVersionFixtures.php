<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SoftwareVersion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SoftwareVersionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $json = file_get_contents(__DIR__ . '/data/softwareversions.json');
        $versions = json_decode($json, true);

        foreach ($versions as $entry) {
            $sv = new SoftwareVersion();
            $sv->setName($entry['name']);
            $sv->setSystemVersion($entry['system_version']);
            $sv->setSystemVersionAlt($entry['system_version_alt']);
            $sv->setLink($entry['link'] ?? '');
            $sv->setSt($entry['st'] ?? '');
            $sv->setGd($entry['gd'] ?? '');
            $sv->setIsLatest($entry['latest'] ?? false);

            if ($entry['latest'] ?? false) {
                $isLCI = str_starts_with($entry['name'], 'LCI');
                $sv->setLatestDisplayVersion($isLCI ? 'v3.4.4' : 'v3.3.7');
            }

            $manager->persist($sv);
        }

        $manager->flush();
    }
}
