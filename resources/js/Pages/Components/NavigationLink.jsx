const navigation = [
  { name: 'Home', href: '/', current: false },
  { name: 'Contact', href: '/contact', current: false },
  { name: 'Movies ', href: '/movies', current: false },
]

function classNames(...classes) {
  return classes.filter(Boolean).join(' ')
}

export default function NavigationLink() {
    return(
        <div className="ml-10 flex items-baseline space-x-4">
            {navigation.map((item) => (
                <a
                    key={item.name}
                    href={item.href}
                    aria-current={item.current ? 'page' : undefined}
                    className={classNames(
                        item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white',
                        'rounded-md px-3 py-2 text-sm font-medium',
                    )}
                >
                    {item.name}
                </a>
            ))}
        </div>
    )
}