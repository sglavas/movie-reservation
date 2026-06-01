import { Disclosure, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react'
import { BellIcon } from '@heroicons/react/24/outline'
import NavigationLink from './NavigationLink'
import { Link, usePage } from '@inertiajs/react'
import Button from '../Components/Button'
import UserAuthControls from './UserAuthControls'
import AdminControls from './AdminControls'

const user = {
  name: 'Tom Cook',
  email: 'tom@example.com',
  imageUrl:
    'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
}

const userNavigation = [
  { name: 'Your profile', href: '#' },
  { name: 'Settings', href: '#' },
  { name: 'Sign out', href: '#' },
]


export default function Layout(props) {
  console.log(usePage());
  return (
    <div>
      <div className="min-h-full bg-slate-900 text-white">
        <Disclosure as="nav" className="relative bg-gray-800">
          <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div className="flex h-16 items-center justify-between gap-20">
              <div className="flex items-center gap-5">
                <div className="shrink-0">
                  <img
                    alt="Movie Theater"
                    src="https://i.imgur.com/opNXcID.png"
                    className="w-12"
                  />
                </div>
                <div>
                    <h1 className='text-white'>ANDROMEDA CINEMA</h1>
                </div>
                <div className="hidden md:block">
                    <NavigationLink 
                      navigation={[{ name: 'Home', href: '/' }, { name: 'Contact', href: '/contact' }, { name: 'Showtimes', href: '/showtimes' }]}
                    />
                </div>
              </div>
              <div className="hidden md:block">
                <div>
                  <UserAuthControls />
                </div>
              </div>

            </div>
          </div>

        </Disclosure>

        <header className="relative bg-gradient-to-b from-gray-800 to-transparent shadow">
          <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div className='flex justify-between items-center'>
              <h1 className='font-bold text-2xl'>{props.slot}</h1>
              <AdminControls />
            </div>
          </div>
        </header>
        <main>
          <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">{props.children}</div>
        </main>
      </div>
    </div>
  )
}
