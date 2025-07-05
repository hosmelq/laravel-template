interface WelcomeProps {
  now: string
}

export default function Welcome({now}: WelcomeProps) {
  return (
    <div className="flex min-h-screen items-center justify-center">
      <h1 className="text-3xl font-bold underline">Welcome - {now}</h1>
    </div>
  )
}
