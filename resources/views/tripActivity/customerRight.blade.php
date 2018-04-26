<section>
    <p class="trip-activity-section-title h3" style="font-weight: 400">注意事項</p>
    <!--成團規則-->
    <ul class="ul_list_item_dot">
        <li>成功付款不等於購買活動門票成功，狀態顯示 <span class="label label-success">成團</span> 才等於成功購票</li>
        <li>如已過成團期限時狀態不是顯示<span class="label label-success">成團</span>，代表「參加」失敗，費用全額退回。</li>
        <li>狀態顯示 <span class="label label-default">購票處理中</span>，代表Pneko正在向商家付款購買門票，商家有任何理由拒絕售賣產品。如果購買失敗，代表「成團失敗」，費用全額退回。</li>
        @foreach($trip_activity['customer_rights'] as $customer_right)
        <li>{{$customer_right->desc}}</li>
        @endforeach
    </ul>
</section>