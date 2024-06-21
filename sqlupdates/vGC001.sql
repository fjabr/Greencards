CREATE TABLE `invitation_links` (
  `id` int(11) NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nb_members` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `invitation_links`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invitation_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `invited_customers_by_links` (
  `id` int(11) NOT NULL,
  `invitation_link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `invited_customers_by_links`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invited_customers_by_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
