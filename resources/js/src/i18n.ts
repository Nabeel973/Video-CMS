import { createI18n } from "vue-i18n";
import messages from "@intlify/unplugin-vue-i18n/messages";

export default createI18n({
    legacy: false,
    allowComposition: true,
    locale: "en",
    globalInjection: true,
    fallbackLocale: "en",
    messages,
});
