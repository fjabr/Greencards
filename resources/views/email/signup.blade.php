<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<style>
			body {
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				text-align: center;
				color: #777;
			}

			body h1 {
				font-weight: 300;
				margin-bottom: 0px;
				padding-bottom: 0px;
				color: #000;
			}

			body h3 {
				font-weight: 300;
				margin-top: 10px;
				margin-bottom: 20px;
				font-style: italic;
				color: #555;
			}

			body a {
				color: #06f;
			}

			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
				border-collapse: collapse;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}
		</style>
	</head>

	<body>

		<div class="invoice-box">
			<table>
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
									<img src="https://greencard-sa.com/public/uploads/all/4FYHed0ZDO5msGHah3bGPo2BBJjzMu8Oj2Qfbtb0.png" alt="Green Card SA" style="width: 80%; max-width: 200px" />
								</td>

								<td>
									Welcome to GREEN CARD<br />
									YOUR ACCOUNT WAS CREATED SUCCESSFULY!<br />
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="heading">
					<td>Email/User name</td>

					<td>Password</td>
				</tr>

				<tr class="item">
					<td>{{ $email }}</td>

					<td>{{ $password }}</td>
				</tr>
			</table>
		</div>
        <div class="invoice-box">
			<table>
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
									<img src="https://greencard-sa.com/public/uploads/all/4FYHed0ZDO5msGHah3bGPo2BBJjzMu8Oj2Qfbtb0.png" alt="Green Card SA" style="width: 80%; max-width: 200px" />
								</td>

								<td>
									مرحبًا بك في البطاقة الخضراء<br />
									تم إنشاء حسابك بنجاح<br />
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="heading">
					<td>البريد الإلكتروني / اسم المستخدم</td>

					<td>كلمة المرور</td>
				</tr>

				<tr class="item">
					<td>{{ $email }}</td>

					<td>{{ $password }}</td>
				</tr>
			</table>
		</div>
        <div style="margin-top: 15px;">
            <a href="{{ get_setting('play_store_link') }}" target="_blank">
                <img src="{{ URL::asset('assets/img/play.png') }}" style ="width: 80%; max-width: 200px;">
            </a>
            <a href="{{ get_setting('app_store_link') }}" target="_blank">
                <img src="{{ URL::asset('assets/img/app.png') }}" style ="width: 80%; max-width: 200px;">
            </a>
        </div>
	</body>
</html>
