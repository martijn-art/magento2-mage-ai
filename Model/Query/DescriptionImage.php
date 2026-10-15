<?php
/**
 * Mageprince
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageprince.com license that is
 * available through the world-wide-web at this URL:
 * https://mageprince.com/end-user-license-agreement
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageprince
 * @package     Mageprince_MageAI
 * @copyright   Copyright (c) Mageprince (https://mageprince.com/)
 * @license     https://mageprince.com/end-user-license-agreement
 */

namespace Mageprince\MageAI\Model\Query;

/**
 * Loads a product gallery image and prepares it for a text-generation (vision) request.
 *
 * The image is downscaled to MAX_DIMENSION on its longest side and re-encoded as JPEG.
 * That keeps requests well under every provider's size limits and keeps token cost low,
 * while leaving plenty of detail for describing colour, material and shape.
 */
class DescriptionImage
{
    public const MAX_DIMENSION = 1024;
    private const JPEG_QUALITY = 85;
    private const MAX_UNRESIZED_BYTES = 4 * 1024 * 1024;
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * @var ImageStorageInterface
     */
    private $imageStorage;

    /**
     * @param ImageStorageInterface $imageStorage
     */
    public function __construct(ImageStorageInterface $imageStorage)
    {
        $this->imageStorage = $imageStorage;
    }

    /**
     * Load a gallery image by its gallery "file" value (e.g. /k/l/klompen.jpg or /m/a/new.jpg.tmp)
     *
     * @param string $file
     * @return array{mimeType: string, base64: string}
     * @throws QueryException
     */
    public function load(string $file): array
    {
        $file = trim($file);
        $relative = substr($file, -4) === '.tmp' ? substr($file, 0, -4) : $file;
        $ext = strtolower(pathinfo($relative, PATHINFO_EXTENSION));

        if ($file === '' || strpos($file, '..') !== false || strpos($file, "\0") !== false
            || !in_array($ext, self::ALLOWED_EXTENSIONS, true)
        ) {
            throw new QueryException(__('The selected product image is not a supported image file.'));
        }

        $original = $this->imageStorage->readOriginal($file);
        $resized = $this->resize($original['data']);

        if ($resized !== null) {
            return ['mimeType' => 'image/jpeg', 'base64' => base64_encode($resized)];
        }

        // GD unavailable or the format could not be decoded: send the original if it is small enough.
        if (strlen($original['data']) > self::MAX_UNRESIZED_BYTES) {
            throw new QueryException(__('The product image is too large to send and could not be resized.'));
        }

        return ['mimeType' => $original['mimeType'], 'base64' => base64_encode($original['data'])];
    }

    /**
     * Downscale and re-encode as JPEG; returns null when GD cannot process the image
     *
     * @param string $data
     * @return string|null
     */
    private function resize(string $data): ?string
    {
        if (!function_exists('imagecreatefromstring')) {
            return null;
        }

        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
        $source = @imagecreatefromstring($data);
        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, self::MAX_DIMENSION / max($width, $height, 1));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $target = imagecreatetruecolor($newWidth, $newHeight);
        // Flatten transparency onto white, as product photos are normally shown on white.
        imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255));
        imagecopyresampled($target, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($target, null, self::JPEG_QUALITY);
        $jpeg = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        return $jpeg !== '' ? $jpeg : null;
    }
}
