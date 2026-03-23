<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\SoftwareVersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SoftwareController extends AbstractController
{
    #[Route('/api/carplay/software/version', name: 'software_version_check', methods: ['POST'])]
    public function checkVersion(Request $request, SoftwareVersionRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $version = $data['version'] ?? '';
        $hwVersion = $data['hwVersion'] ?? '';

        if (empty($version)) {
            return new JsonResponse(['msg' => 'Version is required'], 200);
        }

        if (empty($hwVersion)) {
            return new JsonResponse(['msg' => 'HW Version is required'], 200);
        }

        $patternST = '/^CPAA_[0-9]{4}\.[0-9]{2}\.[0-9]{2}(_[A-Z]+)?$/i';
        $patternGD = '/^CPAA_G_[0-9]{4}\.[0-9]{2}\.[0-9]{2}(_[A-Z]+)?$/i';

        $patternLCI_CIC = '/^B_C_[0-9]{4}\.[0-9]{2}\.[0-9]{2}$/i';
        $patternLCI_NBT = '/^B_N_G_[0-9]{4}\.[0-9]{2}\.[0-9]{2}$/i';
        $patternLCI_EVO = '/^B_E_G_[0-9]{4}\.[0-9]{2}\.[0-9]{2}$/i';

        $hwVersionValid = false;
        $stBool = false;
        $gdBool = false;
        $isLCI = false;
        $lciHwType = '';

        if (preg_match($patternST, $hwVersion)) {
            $hwVersionValid = true;
            $stBool = true;
        }

        if (preg_match($patternGD, $hwVersion)) {
            $hwVersionValid = true;
            $gdBool = true;
        }

        if (preg_match($patternLCI_CIC, $hwVersion)) {
            $hwVersionValid = true;
            $isLCI = true;
            $lciHwType = 'CIC';
            $stBool = true;
        } elseif (preg_match($patternLCI_NBT, $hwVersion)) {
            $hwVersionValid = true;
            $isLCI = true;
            $lciHwType = 'NBT';
            $gdBool = true;
        } elseif (preg_match($patternLCI_EVO, $hwVersion)) {
            $hwVersionValid = true;
            $isLCI = true;
            $lciHwType = 'EVO';
            $gdBool = true;
        }

        if (!$hwVersionValid) {
            return new JsonResponse(['msg' => 'There was a problem identifying your software. Contact us for help.'], 200);
        }

        if (str_starts_with($version, 'v') || str_starts_with($version, 'V')) {
            $version = substr($version, 1);
        }

        $softwareVersions = $repository->findBySystemVersionAlt($version);

        $response = [];
        foreach ($softwareVersions as $row) {
            $isLCIEntry = (str_starts_with($row->getName(), 'LCI'));

            if ($isLCI !== $isLCIEntry) {
                continue;
            }

            if ($isLCI && false === stripos($row->getName(), $lciHwType)) {
                continue;
            }

            if ($row->isLatest()) {
                $response = [
                    'versionExist' => true,
                    'msg' => 'Your system is upto date!',
                    'link' => '',
                    'st' => '',
                    'gd' => '',
                ];
            } else {
                $stLink = '';
                $gdLink = '';
                if ($stBool) {
                    $stLink = $row->getSt();
                }

                if ($gdBool) {
                    $gdLink = $row->getGd();
                }

                $latestMsg = $repository->findLatestDisplayVersion($isLCI);
                if (!$latestMsg) {
                    $latestMsg = $isLCI ? 'v3.4.4' : 'v3.3.7';
                }

                $response = [
                    'versionExist' => true,
                    'msg' => 'The latest version of software is ' . $latestMsg . ' ',
                    'link' => $row->getLink(),
                    'st' => $stLink,
                    'gd' => $gdLink,
                ];
            }
            break;
        }

        if ($response) {
            return new JsonResponse($response, 200);
        }

        return new JsonResponse([
            'versionExist' => false,
            'msg' => 'There was a problem identifying your software. Contact us for help.',
            'link' => '',
            'st' => '',
            'gd' => '',
        ], 200);
    }
}
