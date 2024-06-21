ALTER TABLE `invitation_links` ADD `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' AFTER `shop_id`;
ALTER TABLE `invitation_links` ADD `package_id` int NOT NULL  AFTER `description`;
ALTER TABLE `invitation_links` ADD `deleted` int NOT NULL DEFAULT 0 AFTER `package_id`;

ALTER TABLE `invitation_links` ADD `partner` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' AFTER `deleted`;
ALTER TABLE `invitation_links` ADD `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''  AFTER `partner`;

INSERT INTO `permissions` ( `name`, `section`, `guard_name`, `created_at`, `updated_at`) VALUES
 ( 'invitation_link', 'invitation_link', 'web', '2022-06-14 23:59:28', '2022-06-14 23:59:28');

 INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) VALUES (NULL, 'owner_logo', '1915', NULL, current_timestamp(), current_timestamp());


