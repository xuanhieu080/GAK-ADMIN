import {acceptHMRUpdate, defineStore} from 'pinia';

function focusTrap(element) {
    const focusableElements = getFocusableElements(element)
    const firstFocusableEl = focusableElements[0]
    const lastFocusableEl = focusableElements[focusableElements.length - 1]

    // Wait for the case the element was not yet rendered
    setTimeout(() => firstFocusableEl.focus(), 50)

    /**
     * Get all focusable elements inside `element`
     * @param {HTMLElement} element - DOM element to focus trap inside
     * @return {HTMLElement[]} List of focusable elements
     */
    function getFocusableElements(element = document) {
        return [
            ...element.querySelectorAll(
                'a, button, details, input, select, textarea, [tabindex]:not([tabindex="-1"])'
            ),
        ].filter((e) => !e.hasAttribute('disabled'))
    }

    function handleKeyDown(e) {
        const TAB = 9
        const isTab = e.key.toLowerCase() === 'tab' || e.keyCode === TAB

        if (!isTab) return

        if (e.shiftKey) {
            if (document.activeElement === firstFocusableEl) {
                lastFocusableEl.focus()
                e.preventDefault()
            }
        } else {
            if (document.activeElement === lastFocusableEl) {
                firstFocusableEl.focus()
                e.preventDefault()
            }
        }
    }

    element.addEventListener('keydown', handleKeyDown)

    return function cleanup() {
        element.removeEventListener('keydown', handleKeyDown)
    }
}

export const useStore = defineStore('sidebar', {
    // arrow function recommended for full type inference
    state: () => ({
        isToggleMenu: true,
        dark: 'theme-dark',
        isSideMenuOpen: false,
        isNotificationsMenuOpen: false,
        isProfileMenuOpen: false,
        isPagesMenuOpen: false,
        // Modal
        isModalOpen: false,
        trapCleanup: null,
        isToggleDesktop: false,
        onForgetPassword: false,
        onNewPassword: false,
        email: '',
        otp: '',
    }),
    actions: {
        closeToggleDesktop() {
            this.isToggleDesktop = !this.isToggleDesktop;
        },
        closeToggle() {
            this.isProfileMenuOpen = false
            this.isNotificationsMenuOpen = false
            this.isProfileMenuOpen = false
            this.isSideMenuOpen = false;
        },
        closePopup() {
            this.isToggleMenu = true;
            this.isSideMenuOpen = false;
            this.isNotificationsMenuOpen = false;
            this.isProfileMenuOpen = false;
            this.isPagesMenuOpen = false;
            // Modal
            this.isModalOpen = false;
            this.trapCleanup = null;
        },
        toggleMenu() {
            this.closeToggle();
            this.isToggleMenu = !this.isToggleMenu
        },
        toggleProfileMenu() {
            this.isProfileMenuOpen = !this.isProfileMenuOpen
            this.isToggleMenu = true
        },
        closeProfileMenu() {
            this.isProfileMenuOpen = false
        },
        toggleSideMenu() {
            this.closeToggle();
            this.isSideMenuOpen = !this.isSideMenuOpen
            this.isToggleMenu = !this.isToggleMenu
        },
        closeSideMenu() {
            this.isSideMenuOpen = false
        },
        toggleNotificationsMenu() {
            this.closeToggle();
            this.isNotificationsMenuOpen = !this.isNotificationsMenuOpen
        },
        closeNotificationsMenu() {
            this.isNotificationsMenuOpen = false
        },
        togglePagesMenu() {
            this.isPagesMenuOpen = !this.isPagesMenuOpen
        },
        openModal() {
            this.isModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('.modal'))
        },
        closeModal() {
            this.isModalOpen = false
            this.trapCleanup = null
        },
        toggleTheme() {
            this.dark = !this.dark
            this.setThemeToLocalStorage(this.dark)
        },
        getThemeFromLocalStorage() {
            // if user already changed the theme, use it
            if (window.localStorage.getItem('dark')) {
                return JSON.parse(window.localStorage.getItem('dark'))
            }

            // else return their preferences
            return (
                !!window.matchMedia &&
                window.matchMedia('(prefers-color-scheme: dark)').matches
            )
        },
        setThemeToLocalStorage(value) {
            window.localStorage.setItem('dark', 'true')
        },

    }
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useStore, import.meta.hot))
}
