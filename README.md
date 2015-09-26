# wp railways import <taxonomy>

[![Build Status](https://travis-ci.org/miya0001/railways-importer.svg)](https://travis-ci.org/miya0001/railways-importer)

路線、駅データを指定したカスタムタクソノミーにタームとして登録するWP-CLI用コマンド。

![](https://www.evernote.com/l/ABVKkb-IMb5N47aEDIUqAWFhTcv_ee26qaEB/image.png)

## 駅、路線データについて

以下のURLから入手して、`data/`以下に設置してください。

http://www.ekidata.jp/

ファイル名に変更があった場合は`Util.php`内で指定されているファイル名を変更すること。

## 使い方

```
$ wp railways import <taxonomy>
```

## タクソノミーの構造は以下のとおり

```
─ 都道府県
  ├─ 路線
     ├─ 駅
```

* 路線は都道府県ごとに重複して同じ路線名のタームが登録されています。
* 駅は路線ごとに重複して同じ駅名のタームが登録されています。
* 駅名のタームには、違う路線の同じ駅に対して共通の値が`term_group`に保存されています。

## インストール

```
$ git clone <repositry>
$ cd <repositry>
$ composer install
```

## Requires

* PHP 5.4 or later
* WordPress 4.3 or later

## 謝辞

* http://www.ekidata.jp/
* http://csv.thephpleague.com/
