INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) VALUES (NULL, 'seller_custumer_subscription', '3', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

ALTER TABLE `customers` ADD `registration_source` TEXT NOT NULL AFTER `created_by`;
