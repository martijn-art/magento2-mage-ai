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

namespace Mageprince\MageAI\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public const XML_PATH_IS_ENABLED = 'mageai/general/enabled';
    public const XML_PATH_BASELINE_PROMPT = 'mageai/general/baseline_prompt';
    public const XML_PATH_PROVIDER = 'mageai/api/provider';
    public const XML_PATH_API_BASE_URL = 'mageai/api/base_url';
    public const XML_PATH_API_KEY = 'mageai/api/api_secret';
    public const XML_PATH_API_MODEL = 'mageai/api/model';
    public const XML_PATH_ANTHROPIC_BASE_URL = 'mageai/api/anthropic_base_url';
    public const XML_PATH_ANTHROPIC_API_KEY = 'mageai/api/anthropic_api_secret';
    public const XML_PATH_ANTHROPIC_MODEL = 'mageai/api/anthropic_model';
    public const XML_PATH_GEMINI_BASE_URL = 'mageai/api/gemini_base_url';
    public const XML_PATH_GEMINI_API_KEY = 'mageai/api/gemini_api_secret';
    public const XML_PATH_GEMINI_MODEL = 'mageai/api/gemini_model';
    public const XML_PATH_OPENROUTER_BASE_URL = 'mageai/api/openrouter_base_url';
    public const XML_PATH_OPENROUTER_API_KEY = 'mageai/api/openrouter_api_secret';
    public const XML_PATH_OPENROUTER_MODEL = 'mageai/api/openrouter_model';
    public const XML_PATH_OPENROUTER_SITE_NAME = 'mageai/api/openrouter_site_name';
    public const XML_PATH_PRODUCT_ATTRIBUTE = 'mageai/product_description/attribute';
    public const XML_PATH_TEMPERATURE = 'mageai/product_description/temperature';
    public const XML_PATH_DESCRIPTION_PROMPT = 'mageai/product_description/description_prompt';
    public const XML_PATH_DESCRIPTION_MAX_TOKENS = 'mageai/product_description/description_max_tokens';
    public const XML_PATH_SHORT_SHORT_DESCRIPTION_PROMPT = 'mageai/product_description/short_description_prompt';
    public const XML_PATH_SHORT_DESCRIPTION_MAX_TOKENS = 'mageai/product_description/short_description_max_tokens';
    public const XML_PATH_IMAGE_ATTRIBUTE = 'mageai/image_generation/attribute';

    /**
     * Config groups holding the image generation settings
     *
     * Products and categories have an identical field set (prompts, models, size, quality) under
     * their own group, so every image getter takes the group it should read from.
     */
    public const GROUP_PRODUCT_IMAGE = 'image_generation';
    public const GROUP_CATEGORY_IMAGE = 'category_image_generation';

    /**
     * Get config value
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    /**
     * Check if extension is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED);
    }

    /**
     * Get the global baseline (system) prompt applied to every text generation request
     *
     * Merchant-configured brand voice / compliance / SEO instructions. Empty by default.
     *
     * @return string
     */
    public function getBaselinePrompt(): string
    {
        return trim((string) $this->getConfig(self::XML_PATH_BASELINE_PROMPT));
    }

    /**
     * Get selected AI provider
     *
     * @return string  'openai' or 'anthropic'
     */
    public function getProvider()
    {
        return (string) $this->getConfig(self::XML_PATH_PROVIDER) ?: 'openai';
    }

    /**
     * Get OpenAI API base URL
     *
     * @return string
     */
    public function getApiBaseUrl()
    {
        return $this->getConfig(self::XML_PATH_API_BASE_URL);
    }

    /**
     * Get OpenAI API secret
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->getConfig(self::XML_PATH_API_KEY);
    }

    /**
     * Get OpenAI model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->getConfig(self::XML_PATH_API_MODEL);
    }

    /**
     * Get Anthropic API base URL
     *
     * @return string
     */
    public function getAnthropicBaseUrl()
    {
        return $this->getConfig(self::XML_PATH_ANTHROPIC_BASE_URL);
    }

    /**
     * Get Anthropic API secret
     *
     * @return string
     */
    public function getAnthropicApiSecret()
    {
        return $this->getConfig(self::XML_PATH_ANTHROPIC_API_KEY);
    }

    /**
     * Get Anthropic model
     *
     * @return string
     */
    public function getAnthropicModel()
    {
        return $this->getConfig(self::XML_PATH_ANTHROPIC_MODEL);
    }

    /**
     * Get Gemini API base URL
     *
     * @return string
     */
    public function getGeminiBaseUrl()
    {
        return $this->getConfig(self::XML_PATH_GEMINI_BASE_URL);
    }

    /**
     * Get Gemini API secret
     *
     * @return string
     */
    public function getGeminiApiSecret()
    {
        return $this->getConfig(self::XML_PATH_GEMINI_API_KEY);
    }

    /**
     * Get Gemini model
     *
     * @return string
     */
    public function getGeminiModel()
    {
        return $this->getConfig(self::XML_PATH_GEMINI_MODEL);
    }

    /**
     * Get OpenRouter base URL
     *
     * @return string
     */
    public function getOpenRouterBaseUrl()
    {
        return rtrim((string) $this->getConfig(self::XML_PATH_OPENROUTER_BASE_URL), '/');
    }

    /**
     * Get OpenRouter API key
     *
     * @return string
     */
    public function getOpenRouterApiSecret()
    {
        return $this->getConfig(self::XML_PATH_OPENROUTER_API_KEY);
    }

    /**
     * Get OpenRouter model slug (e.g. anthropic/claude-sonnet-5)
     *
     * @return string
     */
    public function getOpenRouterModel()
    {
        return trim((string) $this->getConfig(self::XML_PATH_OPENROUTER_MODEL));
    }

    /**
     * Get the app name sent to OpenRouter for usage attribution
     *
     * @return string
     */
    public function getOpenRouterSiteName()
    {
        return trim((string) $this->getConfig(self::XML_PATH_OPENROUTER_SITE_NAME));
    }

    /**
     * Get description prompt
     *
     * @return string
     */
    public function getDescriptionPrompt()
    {
        return $this->getConfig(self::XML_PATH_DESCRIPTION_PROMPT);
    }

    /**
     * Get short description prompt
     *
     * @return string
     */
    public function getShortDescriptionPrompt()
    {
        return $this->getConfig(self::XML_PATH_SHORT_SHORT_DESCRIPTION_PROMPT);
    }

    /**
     * Get sampling temperature
     *
     * @return float
     */
    public function getTemperature(): float
    {
        return (float) ($this->getConfig(self::XML_PATH_TEMPERATURE) ?? 0.5);
    }

    /**
     * Get max tokens for a description type
     *
     * @param string $type  'short' or 'full'
     * @return int
     */
    public function getMaxTokens(string $type): int
    {
        $path = $type === 'short'
            ? self::XML_PATH_SHORT_DESCRIPTION_MAX_TOKENS
            : self::XML_PATH_DESCRIPTION_MAX_TOKENS;
        return (int) ($this->getConfig($path) ?: 2048);
    }

    /**
     * Get selected product attribute codes as an array
     *
     * @return string[]
     */
    public function getProductAttributes(): array
    {
        $value = (string) $this->getConfig(self::XML_PATH_PRODUCT_ATTRIBUTE);
        return array_filter(array_map('trim', explode(',', $value)));
    }

    /**
     * Get selected product attribute codes for image generation as an array
     *
     * Product images only — category prompts have no attribute selection.
     *
     * @return string[]
     */
    public function getImageAttributes(): array
    {
        $value = (string) $this->getConfig(self::XML_PATH_IMAGE_ATTRIBUTE);
        return array_filter(array_map('trim', explode(',', $value)));
    }

    /**
     * Get default image generation prompt template
     *
     * @param string $group  GROUP_PRODUCT_IMAGE or GROUP_CATEGORY_IMAGE
     * @return string
     */
    public function getImageDefaultPrompt(string $group = self::GROUP_PRODUCT_IMAGE): string
    {
        return (string) ($this->getImageConfig($group, 'default_prompt') ?: '');
    }

    /**
     * Get default image modification prompt template
     *
     * @param string $group  GROUP_PRODUCT_IMAGE or GROUP_CATEGORY_IMAGE
     * @return string
     */
    public function getImageModifyDefaultPrompt(string $group = self::GROUP_PRODUCT_IMAGE): string
    {
        return (string) ($this->getImageConfig($group, 'modify_default_prompt') ?: '');
    }

    /**
     * Get OpenAI image generation model
     *
     * @param string $group  GROUP_PRODUCT_IMAGE or GROUP_CATEGORY_IMAGE
     * @return string
     */
    public function getImageModel(string $group = self::GROUP_PRODUCT_IMAGE): string
    {
        return (string) ($this->getImageConfig($group, 'openai_image_model') ?: 'gpt-image-2.5-flare');
    }

    /**
     * Get the configured image size (all GPT Image models share the same size set)
     *
     * @param string $group  GROUP_PRODUCT_IMAGE or GROUP_CATEGORY_IMAGE
     * @return string
     */
    public function getImageSize(string $group = self::GROUP_PRODUCT_IMAGE): string
    {
        return (string) ($this->getImageConfig($group, 'gpt_image_size') ?: '1024x1024');
    }

    /**
     * Get the configured OpenAI image quality (low / medium / high / auto)
     *
     * Lower quality generates significantly faster.
     *
     * @param string $group  GROUP_PRODUCT_IMAGE or GROUP_CATEGORY_IMAGE
     * @return string
     */
    public function getImageQuality(string $group = self::GROUP_PRODUCT_IMAGE): string
    {
        return (string) ($this->getImageConfig($group, 'gpt_image_quality') ?: 'medium');
    }

    /**
     * Get Gemini image model
     *
     * @param string $group  GROUP_PRODUCT_IMAGE or GROUP_CATEGORY_IMAGE
     * @return string
     */
    public function getGeminiImageModel(string $group = self::GROUP_PRODUCT_IMAGE): string
    {
        return (string) ($this->getImageConfig($group, 'gemini_image_model') ?: 'gemini-3.1-flash-image');
    }

    /**
     * Read a field from one of the image generation config groups
     *
     * @param string $group
     * @param string $field
     * @return mixed
     */
    private function getImageConfig(string $group, string $field)
    {
        return $this->getConfig('mageai/' . $group . '/' . $field);
    }
}
