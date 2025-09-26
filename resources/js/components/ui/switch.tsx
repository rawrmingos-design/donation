import * as React from "react"
import { cn } from "@/lib/utils"

interface SwitchProps {
  id?: string;
  name?: string;
  checked?: boolean;
  onCheckedChange?: (checked: boolean) => void;
  className?: string;
  tabIndex?: number;
}

const Switch = React.forwardRef<HTMLInputElement, SwitchProps>(
  ({ className, checked, onCheckedChange, id, name, tabIndex, ...props }, ref) => {
    const [isChecked, setIsChecked] = React.useState(checked || false);

    const handleToggle = () => {
      const newChecked = !isChecked;
      setIsChecked(newChecked);
      onCheckedChange?.(newChecked);
    };

    return (
      <div className="relative inline-flex items-center">
        <input
          type="checkbox"
          id={id}
          name={name}
          tabIndex={tabIndex}
          className="sr-only"
          checked={isChecked}
          onChange={() => {}} // Handled by the div click
          ref={ref}
          {...props}
        />
        <div
          className={cn(
            "relative w-11 h-6 rounded-full cursor-pointer transition-colors duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-300",
            isChecked ? "bg-blue-600" : "bg-gray-300",
            className
          )}
          onClick={handleToggle}
          onKeyDown={(e) => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              handleToggle();
            }
          }}
          tabIndex={tabIndex}
          role="switch"
          aria-checked={isChecked}
          aria-labelledby={id}
        >
          <div
            className={cn(
              "absolute top-[2px] left-[2px] bg-white border border-gray-300 rounded-full h-5 w-5 transition-transform duration-200 ease-in-out shadow-sm",
              isChecked ? "translate-x-5" : "translate-x-0"
            )}
          />
        </div>
      </div>
    )
  }
)

Switch.displayName = "Switch"

export { Switch }
