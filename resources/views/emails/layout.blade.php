<div style="background:#f6f1e7; padding:28px 12px; font-family:Georgia, 'Times New Roman', serif;">
  <div style="max-width:560px; margin:0 auto; background:#fffdf8; border:1px solid #ddd0b8; border-radius:6px; overflow:hidden;">
    <div style="padding:24px 28px 18px; border-bottom:3px solid #a9834f;">
      <img src="{{ asset('assets/images/pohscholar.png') }}" alt="POH Scholarships Scheme" style="height:40px;">
    </div>
    <div style="padding:26px 28px; color:#2c2420; font-size:15px; line-height:1.6;">
      @yield('body')
    </div>
    <div style="padding:16px 28px; background:#f6f1e7; color:#5a4f47; font-size:12px; text-align:center;">
      POH Scholarships Scheme &middot; Since 2009 &middot; {{ setting('contact_address') }}<br>
      {{ setting('contact_phone') }} &middot; {{ setting('contact_email') }}
    </div>
  </div>
</div>
