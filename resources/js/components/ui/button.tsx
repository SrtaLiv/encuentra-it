import { Slot } from '@radix-ui/react-slot';
import { cva, type VariantProps } from 'class-variance-authority';
import * as React from 'react';

import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-xl text-[15px] font-extrabold uppercase tracking-[0.8px] ring-offset-background transition-all focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0',
    {
        variants: {
            variant: {
                default:
                    'bg-primary text-primary-foreground shadow-[0_4px_0_var(--primary-shadow)] hover:brightness-105 active:translate-y-1 active:shadow-none',
                destructive:
                    'bg-destructive text-destructive-foreground shadow-[0_4px_0_color-mix(in_srgb,var(--destructive),black_25%)] active:translate-y-1 active:shadow-none',
                outline:
                    'border-2 border-border bg-card text-foreground shadow-[0_4px_0_var(--border)] hover:bg-accent active:translate-y-1 active:shadow-none',
                secondary: 'bg-sky text-sky-foreground shadow-[0_4px_0_var(--sky-shadow)] hover:brightness-105 active:translate-y-1 active:shadow-none',
                ghost: 'hover:bg-accent hover:text-accent-foreground',
                link: 'text-link normal-case tracking-normal underline underline-offset-4',
            },
            size: {
                default: 'h-11 px-5 py-2',
                sm: 'h-9 rounded-lg px-3 text-[13px]',
                lg: 'h-12 px-8',
                icon: 'h-10 w-10',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement>, VariantProps<typeof buttonVariants> {
    asChild?: boolean;
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(({ className, variant, size, asChild = false, ...props }, ref) => {
    const Comp = asChild ? Slot : 'button';
    return <Comp className={cn(buttonVariants({ variant, size, className }))} ref={ref} {...props} />;
});
Button.displayName = 'Button';

export { Button, buttonVariants };
