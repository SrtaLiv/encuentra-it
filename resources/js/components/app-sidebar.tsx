import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Compass, FileQuestion, GraduationCap, Layers, LayoutGrid, Library } from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    { title: 'Mi panel', url: '/dashboard', icon: LayoutGrid },
    { title: 'Test vocacional', url: '/test', icon: Compass },
    { title: 'Carreras', url: '/carreras', icon: GraduationCap },
    { title: 'Recursos', url: '/recursos', icon: BookOpen },
];

const adminNavItems: NavItem[] = [
    { title: 'Áreas', url: '/admin/areas', icon: Layers },
    { title: 'Carreras', url: '/admin/careers', icon: GraduationCap },
    { title: 'Recursos', url: '/admin/resources', icon: Library },
    { title: 'Test', url: '/admin/quiz', icon: FileQuestion },
];

export function AppSidebar() {
    const { auth } = usePage<SharedData>().props;

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain title="Plataforma" items={mainNavItems} />
                {auth.user?.is_admin && <NavMain title="Admin" items={adminNavItems} />}
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
