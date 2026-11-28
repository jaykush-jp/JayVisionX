<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    protected $fillable = [
        'body', 'image', 'hashtags', 'location', 'platforms', 'scheduled_at', 'status',
        'external_ids', 'error', 'created_by',
    ];

    /** Full caption sent to channels: styled body + hashtags (one line gap removed). */
    public function getCaptionAttribute(): string
    {
        $caption = self::styleForSocial((string) $this->body);
        if ($this->hashtags) {
            $caption .= "\n" . trim($this->hashtags);
        }
        return $caption;
    }

    /**
     * Social platforms (Instagram/Facebook/LinkedIn/Google) don't support
     * Markdown or HTML, so convert common Markdown to clean, styled plain text.
     * Bold and italic markers become real Unicode styled characters, while
     * headings, bullets and links are tidied and extra blank lines collapsed.
     * Returns text that is safe to post on any channel.
     */
    public static function styleForSocial(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", trim($text));

        // [text](url) -> readable text (or the url if no text)
        $text = preg_replace_callback('/\[([^\]]*)\]\(([^)\s]+)\)/', function ($m) {
            $label = trim($m[1]);
            return $label !== '' ? $label : $m[2];
        }, $text);

        // Bullets: "- item" / "* item" / "+ item" -> "• item"
        $text = preg_replace('/^[ \t]*[-*+][ \t]+/m', '• ', $text);

        // Headings: "# Heading" -> bold line
        $text = preg_replace_callback('/^[ \t]{0,3}#{1,6}[ \t]+(.+)$/m', fn ($m) => self::toBold(trim($m[1])), $text);

        // **bold** / __bold__
        $text = preg_replace_callback('/\*\*(.+?)\*\*/s', fn ($m) => self::toBold($m[1]), $text);
        $text = preg_replace_callback('/__(.+?)__/s', fn ($m) => self::toBold($m[1]), $text);

        // *italic* / _italic_
        $text = preg_replace_callback('/(?<!\*)\*(?!\s)(.+?)(?<!\s)\*(?!\*)/s', fn ($m) => self::toItalic($m[1]), $text);
        $text = preg_replace_callback('/(?<![\w_])_(?!\s)(.+?)(?<!\s)_(?![\w_])/s', fn ($m) => self::toItalic($m[1]), $text);

        // Collapse 3+ blank lines to a single blank line.
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }

    /** Map A-Z a-z 0-9 to Unicode bold (Mathematical Sans-Serif Bold). */
    public static function toBold(string $s): string
    {
        return self::mapAlnum($s, 0x1D5D4, 0x1D5EE, 0x1D7EC);
    }

    /** Map letters to Unicode italic (Mathematical Italic); digits unchanged. */
    public static function toItalic(string $s): string
    {
        $out = '';
        foreach (preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
            $c = mb_ord($ch, 'UTF-8');
            if ($ch === 'h') {                     // italic small h is reserved
                $out .= mb_chr(0x210E, 'UTF-8');
            } elseif ($c >= 65 && $c <= 90) {      // A-Z
                $out .= mb_chr(0x1D434 + ($c - 65), 'UTF-8');
            } elseif ($c >= 97 && $c <= 122) {     // a-z
                $out .= mb_chr(0x1D44E + ($c - 97), 'UTF-8');
            } else {
                $out .= $ch;
            }
        }
        return $out;
    }

    private static function mapAlnum(string $s, int $upper, int $lower, int $digit): string
    {
        $out = '';
        foreach (preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
            $c = mb_ord($ch, 'UTF-8');
            if ($c >= 65 && $c <= 90) {
                $out .= mb_chr($upper + ($c - 65), 'UTF-8');
            } elseif ($c >= 97 && $c <= 122) {
                $out .= mb_chr($lower + ($c - 97), 'UTF-8');
            } elseif ($c >= 48 && $c <= 57) {
                $out .= mb_chr($digit + ($c - 48), 'UTF-8');
            } else {
                $out .= $ch;
            }
        }
        return $out;
    }

    protected $casts = [
        'platforms'    => 'array',
        'external_ids' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public const PLATFORMS = [
        'google'    => 'Google Business Profile',
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
    ];
}
