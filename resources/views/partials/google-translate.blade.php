{{-- Chỉ nạp khi chọn tiếng Việt: Google Translate dịch nội dung lấy từ database (chữ cố định đã có sẵn bản dịch trong lang/vi.json). --}}
<div id="google_translate_element" hidden></div>
<script>
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({ pageLanguage: 'en', includedLanguages: 'en,vi', autoDisplay: false }, 'google_translate_element');
    }
</script>
<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
