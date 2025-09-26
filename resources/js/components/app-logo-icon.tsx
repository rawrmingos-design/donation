import { cn } from "@/lib/utils";

export default function AppLogoIcon({ className, src }: { className?: string, src?:string }) {
    return (
        <img src={`${src ? src : '/logo.png'}`} alt="Logo" className={cn(className)} />
    );
}
 