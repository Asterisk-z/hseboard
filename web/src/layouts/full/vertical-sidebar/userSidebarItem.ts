import {
    MenuIcon,
    CircleIcon,
    CircleOffIcon,
    BrandChromeIcon,
    MoodSmileIcon,
    StarIcon,
    AwardIcon
} from 'vue-tabler-icons';

export interface menu {
    header?: string;
    title?: string;
    icon?: any;
    to?: string;
    chip?: string;
    chipBgColor?: string;
    chipColor?: string;
    chipVariant?: string;
    chipIcon?: string;
    children?: menu[];
    disabled?: boolean;
    type?: string;
    subCaption?: string;
}

const userSidebarItem: menu[] = [
    {
        title: 'Dashboard',
        icon: BrandChromeIcon,
        to: '/dashboard'
    },
    {
        title: 'Team Member',
        icon: MenuIcon,
        to: '/team-member'
    },
    // {
    //     title: 'Inbox',
    //     icon: CircleIcon,
    //     to: '/inbox'
    // },
    // {
    //     title: 'Notifications',
    //     icon: CircleOffIcon,
    //     to: '/notifications'
    // },
    {
        title: 'Observations',
        icon: MoodSmileIcon,
        to: '/observations'
    },
    {
        title: 'Account Setting',
        icon: CircleIcon,
        to: '/account-setting'
    },
];

export default userSidebarItem;
