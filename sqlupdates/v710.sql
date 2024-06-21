UPDATE `business_settings` SET `value` = '7.1.0' WHERE `business_settings`.`type` = 'current_version';

COMMIT;

INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) VALUES (NULL, 'owner_company_name', 'Greencard for trading est.', 'en', '2023-05-09 06:21:24', '2023-05-09 06:21:24'), (NULL, 'owner_company_name', 'مؤسسه الكرت الأخضر للتجارة', 'sa', '2023-05-09 06:21:34', '2023-05-09 06:21:34')


INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) VALUES (NULL, 'owner_vat_no', '310710332100003', 'en', '2023-05-09 06:23:01', '2023-05-09 06:23:01')
