<?php

	use Aws\S3\S3Client;
	use Aws\S3\Exception\S3Exception;
	use mikehaertl\wkhtmlto\Image;
	use Spatie\ImageOptimizer\OptimizerChainFactory;
	use WebPConvert\WebPConvert;


/**
	 * Class EssayPreviewGenerator
	 */
	class EssayPreviewGenerator {
		protected string $key;
		protected string $secret;
		protected string $bucketName;
		protected string $bucketFolder;
		protected string $endpoint;

		public function __construct() {
			// Keys for AWS SDK
			$this->key    = 'DO009HD4Z7V8XE3MKF3Z';
			$this->secret = 'Gke2e2pZg0qJYxpEIL2np/bJ6zKMEjHtE+Lo9SYvZ84';

			// Params
			$this->bucketName   = 'wps-static';
			$this->bucketFolder = 'owl-uploads';

			$this->endpoint = 'https://fra1.digitaloceanspaces.com';
		}

		public function uploadToDOS( $tmpFileName, $file ) {

			$filePath = $tmpFileName;
			$keyName  = basename( $filePath );

			// Configure a client using Spaces
			$client = new Aws\S3\S3Client( [
				'version'     => 'latest',
				'region'      => 'fra1',
				'endpoint'    => $this->endpoint,
				'credentials' => [
					'key'    => $this->key,
					'secret' => $this->secret,
				],
			] );

			try {
				// Put on S3
				$client->putObject(
					array(
						'Bucket'       => $this->bucketName,
						'Key'          => $this->bucketFolder . '/' . $keyName,
						'SourceFile'   => $file,
						'StorageClass' => 'REDUCED_REDUNDANCY',
						'ACL'          => 'public-read',
						'Content-Type' => mime_content_type( $file )
					)
				);
			} catch ( S3Exception|Exception $e ) {
				echo $e->getMessage();
			}
		}

		public function createCustomImage( $title, $link, $content, $imageName ) {
			$html = <<<EOD
<!doctype html>
<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <style>
	.clear {
		clear: both;
	}
	@font-face {
		font-family: 'Catamaran';
		font-style: normal;
		font-weight: 400;
		font-display: swap;
		src: url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-regular.eot"); /* IE9 Compat Modes */
		src: local(''),
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-regular.eot?#iefix") format('embedded-opentype'), /* IE6-IE8 */
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-regular.woff2") format('woff2'), /* Super Modern Browsers */
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-regular.woff") format('woff'), /* Modern Browsers */
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-regular.ttf") format('truetype'), /* Safari, Android, iOS */
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-regular.svg#Catamaran") format('svg'); /* Legacy iOS */
	}
	@font-face {
		font-family: 'Catamaran';
		font-style: normal;
		font-weight: 700;
		font-display: swap;
		src: url("https://papersowl.com/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-700.eot"); /* IE9 Compat Modes */
		src: local(''),
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-700.eot?#iefix") format('embedded-opentype'), /* IE6-IE8 */
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-700.woff2") format('woff2'), /* Super Modern Browsers */
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-700.woff") format('woff'), /* Modern Browsers */
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-700.ttf") format('truetype'), /* Safari, Android, iOS */
		url("https://papersowl.com/examples/wp-content/themes/papersowl/assets/fonts/catamaran-v8-tamil_latin-ext_latin-700.svg#Catamaran") format('svg'); /* Legacy iOS */
	}
	.essay-preview {
		background: #F2F9FF;
		border-radius: 12px;
		overflow: hidden;
		position: relative;
		padding: 60px 40px;
		min-height: 650px;
	}
	.essay-preview:before {
		content: '';
		position: absolute;
		left: 0;
		bottom: 0;
		width: 100%;
		height: 73px;
		background: linear-gradient(359.15deg, #F2F9FF 21.57%, rgba(242, 249, 255, 0) 110.4%);
		border-radius: 0px 0px 8px 8px;
	}
	.essay-preview p{
		font-family: 'Catamaran';
		font-style: normal;
		font-weight: 400;
		font-size: 24px;
		line-height: 26px;
		color: #333333;
	}
	.essay-preview-top {
		margin-bottom: 20px;
	}
	.essay-preview-icon {
		width: 80px;
		display: block;
		float: left;
		margin-right: 25px;
	}
	.essay-preview-link {
		font-family: 'Catamaran';
		font-style: normal;
		font-weight: 400;
		font-size: 20px;
		line-height: 24px;
		text-decoration-line: underline;
		color: #1799E5;
		display: block;
	}
	.essay-preview-name {
		font-family: 'Catamaran';
		font-style: normal;
		font-weight: 400;
		font-size: 32px;
		line-height: 38px;
		color: #111111;
		margin-bottom: 10px;
		max-width: 690px;
	}
	.essay-preview-info {
		width: 85%;
		float: left;
	}
	.essay-preview-title {
		font-family: 'Catamaran';
		font-style: normal;
		font-weight: 700;
		font-size: 36px;
		line-height: 28px;
		color: #00466F;
		margin: 0 0 10px;
	}
  </style>
</head>
<body>
 <div class="essay-preview">
	<div class="essay-preview-wrap">
		<div class="essay-preview-top">
			<div class="essay-preview-icon">
				<svg width="80" height="80" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M14.05 13.6151C15.6046 13.6151 16.8657 14.8442 16.8657 16.3607C16.8657 17.8772 15.6046 19.1063 14.05 19.1063C12.4954 19.1063 11.2343 17.8772 11.2343 16.3607C11.2343 14.8442 12.4954 13.6151 14.05 13.6151ZM25.3845 13.6151C26.9391 13.6151 28.2002 14.8442 28.2002 16.3607C28.2002 17.8772 26.9391 19.1063 25.3845 19.1063C23.8298 19.1063 22.5687 17.8772 22.5687 16.3607C22.5687 14.8442 23.8298 13.6151 25.3845 13.6151ZM12.739 2.85961H27.2611C27.5954 2.47653 28.124 1.99908 28.8572 1.52642C30.155 0.689881 31.7213 0.143902 33.5593 0.0245239L33.9369 0L34.1205 0.322625C34.1594 0.390955 34.2294 0.518928 34.326 0.703569C34.4852 1.00788 34.6621 1.36288 34.8523 1.76558C35.3941 2.91267 35.9352 4.21467 36.44 5.64792C38.0614 10.251 38.9479 15.1183 38.7125 19.9976C38.2196 30.2248 30.1041 39.1883 20.9431 39.9439V40H20.3466C20.2786 40 20.2274 39.9986 20.1163 39.9946C20.0488 39.9921 20.0219 39.9913 20.0123 39.9906C19.9781 39.9913 19.9512 39.9921 19.8838 39.9946L19.8486 39.9958C19.7684 39.9987 19.7131 40 19.654 40H19.0574V39.9439C9.89604 39.1884 1.78044 30.2249 1.2875 19.9977C1.05212 15.1183 1.93858 10.251 3.55997 5.64792C4.06481 4.21467 4.60594 2.91267 5.14767 1.76558C5.33786 1.36288 5.51478 1.00788 5.674 0.703569C5.7706 0.518928 5.84058 0.390955 5.87947 0.322625L6.06313 3.46791e-06L6.4407 0.0245239C8.27892 0.143901 9.84531 0.689882 11.143 1.52643C11.8762 1.99907 12.4047 2.47652 12.739 2.85961ZM33.2555 1.21756C31.7985 1.36841 30.5559 1.82607 29.5152 2.49694C29.0662 2.78636 28.6873 3.09542 28.3775 3.40251C28.197 3.58141 28.0829 3.71498 28.0342 3.78101L27.8556 4.02301H12.1443L11.9658 3.78094C11.9171 3.71492 11.803 3.58136 11.6226 3.40247C11.3128 3.09539 10.934 2.78634 10.485 2.49693C9.44435 1.82606 8.20175 1.3684 6.74449 1.21755C6.74194 1.22242 6.73937 1.22732 6.73678 1.23228C6.5847 1.52294 6.41476 1.86393 6.23139 2.25221C5.70582 3.36507 5.17966 4.63105 4.68837 6.02584C3.11244 10.4999 2.25173 15.2258 2.47928 19.943C2.95466 29.8059 10.9331 38.4249 19.6815 38.818L19.927 38.8291C19.9529 38.8283 19.9785 38.8279 20.0123 38.8274C20.0333 38.8278 20.0524 38.8283 20.0746 38.829L20.3191 38.818C29.067 38.4249 37.0453 29.8058 37.5207 19.943C37.7483 15.2258 36.8876 10.4999 35.3116 6.02584C34.8203 4.63105 34.2942 3.36507 33.7686 2.25221C33.5852 1.86393 33.4153 1.52294 33.2632 1.23228C33.2606 1.22733 33.2581 1.22242 33.2555 1.21756ZM19.8573 21.8578C21.6523 22.3737 20.7939 25.2025 20.2427 26.4142C20.0685 26.7964 19.8573 26.8022 19.8573 26.8022C19.8573 26.8022 19.6461 26.7964 19.4719 26.4142C18.9207 25.2025 18.0623 22.3737 19.8573 21.8578ZM14.05 12.3858C11.8022 12.3858 9.97379 14.1687 9.97379 16.3606C9.97379 18.553 11.8022 20.3359 14.05 20.3359C16.2978 20.3359 18.1262 18.553 18.1262 16.3606C18.1262 14.1687 16.2978 12.3858 14.05 12.3858ZM14.05 21.4993C11.1442 21.4993 8.78069 19.194 8.78069 16.3606C8.78069 13.5277 11.1442 11.2224 14.05 11.2224C16.9558 11.2224 19.3193 13.5277 19.3193 16.3606C19.3193 19.194 16.9558 21.4993 14.05 21.4993ZM25.3845 12.3858C23.1367 12.3858 21.3082 14.1687 21.3082 16.3606C21.3082 18.553 23.1367 20.3359 25.3845 20.3359C27.6322 20.3359 29.4607 18.553 29.4607 16.3606C29.4607 14.1687 27.6322 12.3858 25.3845 12.3858ZM25.3845 21.4993C22.4787 21.4993 20.1151 19.194 20.1151 16.3606C20.1151 13.5277 22.4787 11.2224 25.3845 11.2224C28.2902 11.2224 30.6538 13.5277 30.6538 16.3606C30.6538 19.194 28.2902 21.4993 25.3845 21.4993ZM26.443 7.301C27.4159 7.301 28.4694 7.43014 29.5838 7.75124C35.2397 9.38175 37.6969 18.7454 31.5226 24.766C25.8578 30.2904 19.8404 33.6852 19.8404 33.6852C19.8404 33.6852 13.8403 30.2904 8.17484 24.766C2.00055 18.7454 4.45833 9.38175 10.1136 7.75124C11.2286 7.43014 12.2815 7.301 13.2551 7.301C17.163 7.301 19.7753 9.38233 19.8487 9.44108C19.9221 9.38233 22.5344 7.301 26.443 7.301ZM26.443 8.46441C22.9895 8.46441 20.6314 10.32 20.6081 10.3386L19.8487 10.9465L19.0893 10.3386C19.066 10.32 16.7073 8.46441 13.2551 8.46441C12.3215 8.46441 11.3789 8.59994 10.4519 8.86694C8.38065 9.46435 6.74073 11.3683 6.06425 13.9603C5.14019 17.5064 6.2444 21.238 9.01895 23.9429C13.4782 28.2917 18.204 31.3264 19.841 32.3234C21.4815 31.327 26.2193 28.2922 30.6791 23.9429C33.453 21.238 34.5578 17.5064 33.6332 13.9603C32.9573 11.3683 31.3168 9.46435 29.2456 8.86694C28.3191 8.59994 27.376 8.46441 26.443 8.46441Z" fill="url(#paint0_linear_4380_16363)"/>
					<defs>
						<linearGradient id="paint0_linear_4380_16363" x1="1" y1="20" x2="39" y2="20" gradientUnits="userSpaceOnUse">
							<stop stop-color="#1B99E5"/>
							<stop offset="1" stop-color="#FDC420"/>
						</linearGradient>
					</defs>
				</svg>
			</div>
			<span class="essay-preview-info">
				<h3 class="essay-preview-title">PapersOwl</h3>
				<span class="essay-preview-link">{$link}</span>
			</span>
			<div class="clear"></div>
		</div>
		<div class="essay-preview-name">{$title}</div>
		<div class="essay-preview-content">{$content}</div>
	</div>
</div>
</body>
</html>
EOD;
			$saveFolderWebpName = get_stylesheet_directory() . '/images/' . $imageName . '-name.webp';
			$saveFolderWebp     = get_stylesheet_directory() . '/images/' . $imageName . '.webp';

			$image = new Image();
			$image->setPage( $html );
			$image->saveAs( $saveFolderWebpName );

			if ( file_exists( $saveFolderWebpName ) && class_exists('WebPConvert\WebPConvert') ) {
				// Желаемая ширина и высота нового изображения
				$newWidth = 314;
				$newHeight = 309;

				$options = [
					'quality' => 1, // устанавливаем качество изображения (от 0 до 100)
					'resize' => [
						'force' => true,
						'maxWidth' => $newWidth,
						'maxHeight' => $newHeight
					]
				];
				WebPConvert::convert( $saveFolderWebpName, $saveFolderWebp, $options );

				$optimizerChain = OptimizerChainFactory::create();
				$optimizerChain->optimize( $saveFolderWebp );
				$tmpFileName = '/images/' . $imageName . '.webp';
				$this->uploadToDOS( $tmpFileName, $saveFolderWebp );
				unlink( $saveFolderWebp );
				unlink( $saveFolderWebpName );

				return 'Success';
			}

			return 'Failure';
		}

	}
