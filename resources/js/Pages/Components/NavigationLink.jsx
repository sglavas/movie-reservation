import { usePage } from "@inertiajs/react";
import { Link } from "@inertiajs/react";

const navigation = [
  { name: 'Home', href: '/' },
  { name: 'Contact', href: '/contact' },
  { name: 'Showtimes ', href: '/showtimes' },
]


function classNames(...classes) {
    return classes.filter(Boolean).join(' ')
}

export default function NavigationLink() {
    const {path} = usePage().props;
    
    return(
        <div className="ml-10 flex items-baseline space-x-4">
            {navigation.map((item) => (
                <Link
                    key={item.name}
                    href={item.href}
                    aria-current={item.href === usePage().url ? 'page' : undefined}
                    className={classNames(
                        item.href === usePage().url ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white',
                        'rounded-md px-3 py-2 text-sm font-medium',
                    )}
                >
                    {item.name}
                </Link>
            ))}
        </div>
    )
}