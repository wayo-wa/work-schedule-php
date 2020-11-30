#!/bin/sh

yarn config set network-timeout 1000000 #タイムアウト時間を長くしておく
yarn add gulp --dev             # gulpをインストール
yarn global add gulp-cli --dev  # gulpをコマンドから使えるように（グローバルのみインストール）
yarn add gulp-sass --dev        # sassをcssにビルドする
yarn add gulp-sass-glob --dev   # sassファイルのimportをひとつにまとめる
yarn add gulp-clean-css --dev   # css圧縮
yarn add gulp-rename --dev      # 圧縮したcssファイルに.minつける
yarn add gulp-autoprefixer --dev # プレフィックス自動付与
yarn add gulp-plumber           # sassの構文エラーがあってもgulpを止めない
