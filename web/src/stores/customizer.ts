import { defineStore } from "pinia";
import config from '@/config'


export const useCustomizerStore = defineStore({
  id: "customizer",
  state: () => ({
    Sidebar_drawer: config.Sidebar_drawer,
    Customizer_drawer: config.Customizer_drawer,
    mini_sidebar: config.mini_sidebar,
    setHorizontalLayout: config.setHorizontalLayout, // Horizontal layout
    actTheme: config.actTheme,
    boxed: config.boxed,
    setBorderCard: config.setBorderCard
  }),

  getters: {
    textColor: (state) => {
      return state.actTheme == 'AQUA_THEME' ? 'black' : 'white'
    },
  },
  actions: {
    CHANGE_THEME() {
      if (this.actTheme == "DARK_AQUA_THEME") {
        localStorage.setItem('actTheme', 'AQUA_THEME')
        this.actTheme = "AQUA_THEME"
      } else {
        localStorage.setItem('actTheme', 'DARK_AQUA_THEME')
        this.actTheme = "DARK_AQUA_THEME"
      }
    },
    SET_SIDEBAR_DRAWER() {
      this.Sidebar_drawer = !this.Sidebar_drawer;
    },
    SET_MINI_SIDEBAR(payload: any) {
      this.mini_sidebar = payload;
    },
    SET_CUSTOMIZER_DRAWER(payload: any) {
      this.Customizer_drawer = payload;
    },

    SET_LAYOUT(payload: any) {
      this.setHorizontalLayout = payload;
    },
    SET_THEME(payload: any) {
      this.actTheme = payload;
    },
    SET_CARD_BORDER(payload: any){
      this.setBorderCard = payload
    }
  },
});
