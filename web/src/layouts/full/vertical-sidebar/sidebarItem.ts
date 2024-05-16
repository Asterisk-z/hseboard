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

const sidebarItem: menu[] = [
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
        title: 'Actions',
        icon: StarIcon,
        to: '/actions'
    },
    {
        title: 'HSE Statistics',
        icon: AwardIcon,
        to: '/hse-statistics'
    },
    {
        title: 'HSE Investigation',
        icon: MenuIcon,
        to: '/hse-investigation'
    },
    {
        title: 'Audit Management',
        icon: CircleIcon,
        to: '/hse-audit'
    },
    {
        title: 'OSHA Compliance',
        icon: CircleOffIcon,
        to: '/osha-compliance'
    },
    {
        title: 'Facility Inspection',
        icon: MoodSmileIcon,
        to: '/inspections'
    },
    {
        title: 'Job Hazard Analysis',
        icon: StarIcon,
        to: '/hazard-analysis'
    },
    {
        title: 'Permit to Work System',
        icon: AwardIcon,
        to: '/work-permit'
    },
    {
        title: 'Billing',
        icon: MenuIcon,
        to: '/billing'
    },
    {
        title: 'Account Setting',
        icon: CircleIcon,
        to: '/account-setting'
    },
    {
        title: 'Business Setting',
        icon: CircleOffIcon,
        to: '/business-setting'
    },
];

export default sidebarItem;
