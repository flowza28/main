<?php

namespace App\Services;

use phpseclib3\Net\SSH2;

class MikrotikService
{
    public function disconnectUser(string $host, string $username, string $password, string $userToDisconnect): bool
    {
        try {
            $ssh = new SSH2($host);
            if (!$ssh->login($username, $password)) {
                return false;
            }

            // Disconnect PPP user
            $command = "/ppp active remove [find name=\"$userToDisconnect\"]";
            $output = $ssh->exec($command);

            // Also try hotspot if PPP fails
            if (strpos($output, 'no such item') !== false || empty($output)) {
                $command = "/ip hotspot active remove [find user=\"$userToDisconnect\"]";
                $output = $ssh->exec($command);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getActiveUsers(string $host, string $username, string $password): array
    {
        try {
            $ssh = new SSH2($host);
            if (!$ssh->login($username, $password)) {
                return [];
            }

            // Get PPP active users
            $command = "/ppp active print";
            $output = $ssh->exec($command);
            $pppUsers = $this->parseMikrotikOutput($output);

            // Get Hotspot active users
            $command = "/ip hotspot active print";
            $output = $ssh->exec($command);
            $hotspotUsers = $this->parseMikrotikOutput($output);

            return array_merge($pppUsers, $hotspotUsers);
        } catch (\Exception $e) {
            return [];
        }
    }

    private function parseMikrotikOutput(string $output): array
    {
        $lines = explode("\n", trim($output));
        $users = [];
        $headers = [];

        foreach ($lines as $line) {
            if (empty($line)) continue;

            if (strpos($line, 'Flags:') === 0) continue; // Skip flags line

            $parts = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);

            if (count($parts) < 2) continue;

            if (empty($headers)) {
                $headers = $parts;
                continue;
            }

            $user = [];
            foreach ($headers as $index => $header) {
                $user[$header] = $parts[$index] ?? '';
            }
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Get interface traffic data
     */
    public function getInterfaceTraffic(string $host, string $username, string $password, string $interface = 'ether1'): array
    {
        try {
            $ssh = new SSH2($host);
            if (!$ssh->login($username, $password)) {
                return [];
            }

            // Monitor traffic for specific interface
            $command = "/interface monitor-traffic $interface once";
            $output = $ssh->exec($command);

            // Parse output
            $lines = explode("\n", trim($output));
            $data = [];

            foreach ($lines as $line) {
                if (strpos($line, 'rx-bits-per-second:') !== false) {
                    $data['rx_bits_per_second'] = (int) trim(str_replace('rx-bits-per-second:', '', $line));
                }
                if (strpos($line, 'tx-bits-per-second:') !== false) {
                    $data['tx_bits_per_second'] = (int) trim(str_replace('tx-bits-per-second:', '', $line));
                }
                if (strpos($line, 'rx-bytes:') !== false) {
                    $data['rx_bytes'] = (int) trim(str_replace('rx-bytes:', '', $line));
                }
                if (strpos($line, 'tx-bytes:') !== false) {
                    $data['tx_bytes'] = (int) trim(str_replace('tx-bytes:', '', $line));
                }
            }

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get all interfaces traffic
     */
    public function getAllInterfacesTraffic(string $host, string $username, string $password): array
    {
        try {
            $ssh = new SSH2($host);
            if (!$ssh->login($username, $password)) {
                return [];
            }

            // Get all ethernet interfaces
            $command = "/interface ethernet print";
            $output = $ssh->exec($command);
            $interfaces = $this->parseEthernetInterfaces($output);

            $trafficData = [];
            foreach ($interfaces as $interface) {
                $traffic = $this->getInterfaceTraffic($host, $username, $password, $interface);
                if (!empty($traffic)) {
                    $trafficData[$interface] = $traffic;
                }
            }

            return $trafficData;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function parseEthernetInterfaces(string $output): array
    {
        $lines = explode("\n", trim($output));
        $interfaces = [];

        foreach ($lines as $line) {
            if (empty($line) || strpos($line, 'Flags:') === 0 || strpos($line, '#') === 0) continue;

            $parts = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
            if (count($parts) >= 2) {
                $interfaces[] = $parts[1]; // name column
            }
        }

        return $interfaces;
    }
}