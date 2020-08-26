<?php


namespace Xhgui\Profiler;


class ProfilerHelper {
    /**
     * This method processes the profile data to remove any dots (.) that are found
     * in the keys of the profile data.
     *
     * This is because mongoDB does not allow keys to be inserted into the document with . in the
     * key name
     *
     * @param array $profileData The profile data array
     * @return array Return the processes data
     */
    public static function processProfileData(array $profileData): array
    {
        $profile = [];
        foreach ($profileData as $key => $value) {
            $profile[strtr($key, ['.' => '_'])] = $value;
        }
        return $profile;
    }
}