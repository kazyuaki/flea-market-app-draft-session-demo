@component('mail::message')
# 取引完了のお知らせ

{{ $transaction->item->name }} の取引が購入者によって完了されました。

マイページで詳細をご確認ください。

@endcomponent