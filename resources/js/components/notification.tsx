import * as React from "react"
import { cn } from "@/lib/utils"
import { X, AlertCircle, CheckCircle, Info, AlertTriangle } from "lucide-react"

interface NotificationProps {
  type?: 'success' | 'error' | 'warning' | 'info'
  title?: string
  message: string
  onClose?: () => void
  className?: string
}

const notificationStyles = {
  success: "bg-green-50 border-green-200 text-green-800",
  error: "bg-red-50 border-red-200 text-red-800",
  warning: "bg-yellow-50 border-yellow-200 text-yellow-800",
  info: "bg-blue-50 border-blue-200 text-blue-800"
}

const iconMap = {
  success: CheckCircle,
  error: AlertCircle,
  warning: AlertTriangle,
  info: Info
}

export default function Notification({ 
  type = 'info', 
  title, 
  message, 
  onClose, 
  className 
}: NotificationProps) {
  const [isVisible, setIsVisible] = React.useState(true)
  const Icon = iconMap[type]

  const handleClose = () => {
    setIsVisible(false)
    onClose?.()
  }

  if (!isVisible) return null

  return (
    <div className={cn(
      "relative rounded-lg border p-4 shadow-sm",
      notificationStyles[type],
      className
    )}>
      <div className="flex">
        <div className="flex-shrink-0">
          <Icon className="h-5 w-5" />
        </div>
        <div className="ml-3 flex-1">
          {title && (
            <h3 className="text-sm font-medium">
              {title}
            </h3>
          )}
          <div className={cn("text-sm", title && "mt-1")}>
            {message}
          </div>
        </div>
        {onClose && (
          <div className="ml-auto pl-3">
            <div className="-mx-1.5 -my-1.5">
              <button
                type="button"
                onClick={handleClose}
                className={cn(
                  "inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2",
                  type === 'success' && "text-green-500 hover:bg-green-100 focus:ring-green-600",
                  type === 'error' && "text-red-500 hover:bg-red-100 focus:ring-red-600",
                  type === 'warning' && "text-yellow-500 hover:bg-yellow-100 focus:ring-yellow-600",
                  type === 'info' && "text-blue-500 hover:bg-blue-100 focus:ring-blue-600"
                )}
              >
                <span className="sr-only">Dismiss</span>
                <X className="h-4 w-4" />
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  )
}
