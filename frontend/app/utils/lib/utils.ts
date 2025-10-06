import { type ClassValue, clsx } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: (ClassValue | undefined | null | boolean | string)[]) {
  return twMerge(clsx(inputs))
}
