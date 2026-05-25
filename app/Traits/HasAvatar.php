<?php

namespace App\Traits;

use App\Models\User;

trait HasAvatar
{
    /**
     * Get avatar URL attribute.
     */
    public function getAvatarUrlAttribute(): string
    {
        // For User model
        if ($this->table === 'users' || $this->collection === 'users') {
            if ($this->profile_photo) {
                return asset('storage/' . $this->profile_photo);
            }
            if ($this->avatar && (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://'))) {
                return $this->avatar;
            }
            if ($this->avatar) {
                return asset('storage/' . $this->avatar);
            }
            return $this->getInitialsAvatarUrl($this->name);
        }

        // For Guest model
        if ($this->collection === 'guests') {
            $user = User::where('email', $this->email)->first();
            if ($user) {
                return $user->avatar_url;
            }
            return $this->getInitialsAvatarUrl($this->name ?? $this->email);
        }

        // For Vendor model
        if ($this->collection === 'vendors') {
            if ($this->user && $this->user->profile_photo) {
                return asset('storage/' . $this->user->profile_photo);
            }
            if ($this->user && $this->user->avatar) {
                if (str_starts_with($this->user->avatar, 'http://') || str_starts_with($this->user->avatar, 'https://')) {
                    return $this->user->avatar;
                }
                return asset('storage/' . $this->user->avatar);
            }
            if ($this->image_url) {
                if (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://')) {
                    return $this->image_url;
                }
                return asset('storage/' . $this->image_url);
            }
            return $this->getInitialsAvatarUrl($this->business_name ?? $this->name);
        }

        // Default fallback
        return $this->getInitialsAvatarUrl($this->name ?? 'User');
    }

    /**
     * Generate elegant SVGs with background gradient based on username hash.
     */
    public function getInitialsAvatarUrl(?string $name = null): string
    {
        $name = $name ?? 'User';
        $words = explode(' ', trim($name));
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
            if (strlen($initials) >= 2) break;
        }
        if (empty($initials)) {
            $initials = 'U';
        }

        // Cohesive, premium gradient colors
        $colors = [
            ['#FF416C', '#FF4B2B'],
            ['#7F00FF', '#E100FF'],
            ['#396afc', '#2948ff'],
            ['#00c6ff', '#0072ff'],
            ['#f857a6', '#ff5858'],
            ['#56ab2f', '#a8e063'],
            ['#e65c00', '#F9D423'],
            ['#11998e', '#38ef7d']
        ];
        $colorPair = $colors[abs(crc32($name)) % count($colors)];
        
        $svg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'>" .
               "<defs>" .
               "<linearGradient id='grad-" . md5($name) . "' x1='0%' y1='0%' x2='100%' y2='100%'>" .
               "<stop offset='0%' style='stop-color:{$colorPair[0]};stop-opacity:1' />" .
               "<stop offset='100%' style='stop-color:{$colorPair[1]};stop-opacity:1' />" .
               "</linearGradient>" .
               "</defs>" .
               "<circle cx='50' cy='50' r='50' fill='url(#grad-" . md5($name) . ")' />" .
               "<text x='50%' y='55%' dy='.1em' fill='#ffffff' font-size='38' font-weight='bold' font-family='Arial, Helvetica, sans-serif' text-anchor='middle' alignment-baseline='middle'>{$initials}</text>" .
               "</svg>";
               
        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}
