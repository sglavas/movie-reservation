import { ChevronDownIcon } from '@heroicons/react/16/solid'
import { usePage } from '@inertiajs/react'
import { useEffect, createContext } from 'react';

export const FormContext = createContext(); 

export default function ShowtimeInformation ({data, setData}){
    // Implement usePage props to avoid prop drilling
    const { theaters, errors, formInfo} = usePage().props;
    // const {movies, theaters, screens, errors, formInfo} = usePage().props;

    const { movies, screens } = formInfo;

    // Filter the screens when a new theater is selected
    const selectedScreens = screens.filter(screen => screen['theater_id'] == data.theater);

    // Run when another theater is selected
    useEffect(() => {
        // If no screens for the theater exist
        if(selectedScreens.length === 0){
            // Set screen data to empty string
            setData('screen', '');
            return;
        }
        // Set data.screen to the ID of the first one on the list
        setData('screen', selectedScreens[0].id)
    }, [data.theater])


    return(
            <div className="border-b border-white/10 pb-12">
                <h2 className="text-xl font-bold my-10 text-white">Showtime Information</h2>

                <div className="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <div className="sm:col-span-3">
                        <label htmlFor="movie" className="block text-sm/6 font-medium text-white">
                            Movie
                        </label>
                            <div className="mt-2 grid grid-cols-1">
                                <select
                                    id="movie"
                                    name="movie"
                                    className="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white/5 py-1.5 pl-3 pr-8 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 *:bg-gray-800 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6"
                                    value={data.movie}
                                    onChange={e => setData('movie', e.target.value)}
                                    required
                                >
                                    {/* Map over the movies array and display the movie titles */}
                                    {movies.map(movie => (
                                        <option value={movie.id} key={movie.id}>{movie.title}</option>
                                    ))}
                                </select>
                                <ChevronDownIcon
                                aria-hidden="true"
                                className="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-400 sm:size-4"
                                />
                            </div>
                            <div className='text-red-500 text-xs mt-1'>{errors.movie ? errors.movie : ''}</div>
                    </div>
                    <div className="sm:col-span-3">
                        <label htmlFor="theater" className="block text-sm/6 font-medium text-white">
                            Theater
                        </label>
                        <div className="mt-2 grid grid-cols-1">
                            <select
                                id="theater"
                                name="theater"
                                value={data.theater}
                                onChange={e => setData('theater', e.target.value)}
                                autoComplete="theater-name"
                                className="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white/5 py-1.5 pl-3 pr-8 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 *:bg-gray-800 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6"
                                required
                            >
                                {/* Map over the theaters array and display the theater name and the city where it is located */}
                                {theaters.map(theater =>(
                                    <option value={theater.id} key={theater.id}>{theater.name}, {theater.city}</option>
                                ))}
                            </select>
                            <ChevronDownIcon
                            aria-hidden="true"
                            className="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-400 sm:size-4"
                            />
                        </div>
                        <div className='text-red-500 text-xs mt-1'>{errors.theater ? errors.theater : ''}</div>
                    </div>
                    <div className="sm:col-span-3">
                        <label htmlFor="screen" className="block text-sm/6 font-medium text-white">
                            Screen
                        </label>
                        <div className="mt-2 grid grid-cols-1">
                            <select
                                id="screen"
                                name="screen"
                                value={data.screen}
                                autoComplete="screen-name"
                                onChange={e => setData('screen', e.target.value)}
                                className="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white/5 py-1.5 pl-3 pr-8 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 *:bg-gray-800 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6"
                                required
                            >
                                {/* Map over the screens array and display the screen id corresponding to the selected theater */}
                                {
                                    selectedScreens.map(screen => {
                                            return(
                                                <option value={screen.id} key={screen.id}>Screen {screen.label}</option>
                                            )
                                    })
                                }
                            </select>
                            <ChevronDownIcon
                            aria-hidden="true"
                            className="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-400 sm:size-4"
                            />
                            <div className='text-red-500 text-xs mt-1'>{errors.screen ? errors.screen : ''}</div>
                        </div>

                        <div className="flex gap-6 mt-6">
                            <div className="sm:col-span-3">
                                <label htmlFor="date" className="block text-sm/6 font-medium text-white">
                                    Date
                                </label>
                                <input
                                    id="date"
                                    name="date"
                                    type="date"
                                    value={data.date}
                                    aria-describedby="date-description"
                                    onChange={e => setData('date', e.target.value)}
                                    className="col-start-1 row-start-1 appearance-none rounded border border-white/10 bg-white/5 checked:border-indigo-500 checked:bg-indigo-500 indeterminate:border-indigo-500 indeterminate:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 disabled:border-white/5 disabled:bg-white/10 disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                    required
                                />
                                <div className='text-red-500 text-xs mt-1'>{errors.date ? errors.date : ''}</div>

                                
                                
                            </div>
                            <div className="sm:col-span-3">
                                <label htmlFor="time" className="block text-sm/6 font-medium text-white">
                                    Time
                                </label>
                                <input
                                    id="time"
                                    name="time"
                                    type="time"
                                    value={data.time}
                                    aria-describedby="time-description"
                                    onChange={e => setData('time', e.target.value)}
                                    className="col-start-1 row-start-1 appearance-none rounded border border-white/10 bg-white/5 checked:border-indigo-500 checked:bg-indigo-500 indeterminate:border-indigo-500 indeterminate:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 disabled:border-white/5 disabled:bg-white/10 disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                    required
                                />
                                <div className='text-red-500 text-xs mt-1'>{errors.time ? errors.time : ''}</div>
                                
                            </div>
                        </div>
                    </div>


                </div>
            </div>
    )
}